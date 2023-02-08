<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Import;
use App\Helpers\Functions;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Constants\User as ConstantsUser;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class ClientImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    use Importable, RegistersEventListeners;

    public function __construct(
        $log,
        $auth_user,
        $count
    ) {
        $this->log = $log;
        $this->auth_user = $auth_user;
        $this->count = $count;
    }

    public function collection(Collection $rows)
    {
        $i = 1;

        $id_log = $this->log;
        $auth_user_id = $this->auth_user->id;

        $text = "";

        $import_advance = Import::whereCode($id_log)->first();

        if ($import_advance) {
            $i = $import_advance->advance + 1;
        }

        $a = 0;
        $continue = true;

        foreach ($rows as $row) {
            $i++;
            $row = $row->toArray();

            $row["numero_documento"] = strval($row["numero_documento"]);

            if (User::where('document_type', $row["tipo_documento"])->where('document_number', $row["numero_documento"])->count()) {
                $message = "El documento " . $row['tipo_documento'] . " " . $row["numero_documento"] . " ya se encuentra registrado";
                $text .= "Fila " . $i . ": " . $message . "\n";

                continue;
            }

            $validator = Validator::make($row, $this->rules(), [], $this->customValidationAttributes());

            if ($validator->fails()) {
                $message = Functions::getValidatorMessageOneLine($validator);
                $text .= "Fila " . $i . ": " . $message . "\n";
            } else {
                $business = cache()->remember('bus-' . $row['ruc_empresa'], 60, function () use ($row) {
                    return DB::table('businesses')->where('ruc', $row['ruc_empresa'])->first();
                });

                if (!$business) {
                    $message = "El RUC " . $row['ruc_empresa'] . " no se encuentra registrado";
                    $text .= "Fila " . $i . ": " . $message . "\n";

                    continue;
                }

                $marital_status_id = null;

                if ($row['estado_civil']) {
                    switch (strtolower($row['estado_civil'])) {
                        case 'soltero':
                        case 'soltera':
                        case 's':
                            $marital_status_id = 1;
                            break;

                        case 'casado':
                        case 'casada':
                        case 'c':
                            $marital_status_id = 2;
                            break;

                        case 'viudo':
                        case 'viuda':
                        case 'v':
                            $marital_status_id = 3;
                            break;

                        case 'divorciado':
                        case 'divorciada':
                        case 'd':
                            $marital_status_id = 4;
                            break;
                    }
                }

                $user = User::create([
                    "document_type" => $row['tipo_documento'],
                    "document_number" => $row['numero_documento'],
                    "names" => $row['nombres'],
                    "surnames" => $row['apellidos'],
                    "email" => $row['correo_electronico'],
                    "role" => "employee",
                    "business_id" => $business->id,
                    "active" => 1,
                    "salary" => $row['salario'],
                    "address" => $row['direccion'],
                    "business_job" => $row['puesto_trabajo'],
                    "marital_status_id" => $marital_status_id
                ]);

                $l = 0;
                $accountIterate = 1;
                $multiplo = 0;

                $account_bank = null;
                $account_number = null;
                $account_cci = null;

                foreach ($row as $key => $item) {
                    if ($l > 10 && $key != '') {
                        $multiplo++;

                        if ($multiplo == 4) {
                            $accountIterate++;
                            $multiplo = 1;
                            $account_bank = null;
                            $account_number = null;
                            $account_cci = null;
                        }

                        switch ($multiplo) {
                            case '1':
                                if (strstr($key, 'banco_')) {
                                    $account_bank = cache()->remember('bnk-' . $row[$key], 60, function () use ($row, $key) {
                                        return DB::table('banks')
                                            ->where(function ($query) use ($row, $key) {
                                                return $query->where('name', $row[$key])
                                                    ->orWhere('short_name', $row[$key]);
                                            })
                                            ->first();
                                    });

                                    if (!$account_bank) {
                                        if ($accountIterate == 1) {
                                            if (!isset($account_message)) {
                                                $account_message = "Usuario registrado, pero con los siguiente errores: ";
                                            }

                                            $account_message .= "El banco " . $row[$key] . " no existe. ";
                                        }
                                    }
                                }
                                break;

                            case '2':
                                if (strstr($key, 'numero_')) {
                                    if ($row[$key] == '') {
                                        if ($accountIterate == 1) {
                                            if (!isset($account_message)) {
                                                $account_message = "Usuario registrado, pero con los siguiente errores: ";
                                            }

                                            $account_message .= "Número de cuenta inválido. ";
                                        }
                                    } else {
                                        $account_number = $row[$key];
                                    }
                                }
                                break;

                            case '3':
                                if (strstr($key, 'cci_')) {
                                    $account_cci = $row[$key];

                                    if ($account_bank && $account_number) {
                                        DB::table('accounts')->insert([
                                            'user_id' => $user->id,
                                            'bank_id' => $account_bank->id,
                                            'number' => $account_number,
                                            'cci' => $account_cci
                                        ]);
                                    }
                                }
                                break;

                            default:
                                # code...
                                break;
                        }
                    }

                    $l++;
                }

                if (isset($account_message)) {
                    $text .= "Fila " . $i . ": " . $account_message . "\n";
                    unset($account_message);
                }
            }
        }

        $import = Import::UpdateOrCreate(
            [
                'code' => $id_log
            ],
            [
                "model" => "Clients",
                "rows" => $this->count,
                "advance" => DB::raw('advance + ' . count($rows)),
                "admin_id" => $this->auth_user->id ?? 0,
                "status" => "PROCESSING"
            ]
        );

        file_put_contents(storage_path() . '/app/public/import_logs/' . $id_log . '.log', $text . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        $rules = [
            "tipo_documento" => 'required|' . Rule::in(ConstantsUser::$document_type),
            "numero_documento" => 'required',
            "nombres" => 'required',
            "apellidos" => 'required',
            "correo_electronico" => 'required|email|unique:users,email',
            "ruc_empresa" => 'required',
            "estado" => 'required|' . Rule::in(['ACTIVO', 'SUSPENDIDO']),
        ];

        return $rules;
    }

    /**
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            "tipo_documento" => 'tipo de documento',
            "numero_documento" => 'numero de documento',
            "nombres" => 'nombres',
            "apellidos" => 'apellidos',
            "correo_electronico" => 'correo electrónico',
            "ruc_empresa" => 'RUC de empresa',
            "estado" => 'estado',
        ];
    }

    public static function beforeImport(BeforeImport $event)
    {
        $data = $event->getConcernable();
        $email =  $data->auth_user->email ?? "";
        $log = explode("EXH_", $data->log);
        $code =  $data->log;
        $rows =  $data->count;

        $text = "Inicialización de carga masiva de clientes " . $code . "\n\n";
        $myfile = file_put_contents(storage_path() . '/app/public/import_logs/' . $code . '.log', $text . PHP_EOL, FILE_APPEND | LOCK_EX);

        Import::create([
            "model" => "Clients",
            "code" => $code,
            "rows" => $rows,
            "advance" => 0,
            "admin_id" => $data->auth_user->id ?? 99,
            "status" => "PENDIENTE"
        ]);

        // Mail::to($email)->queue(new notificationStartImport($email, $log[1], ""));
    }

    public static function afterImport(AfterImport $event)
    {
        $data = $event->getConcernable();
        $email =  $data->auth_user->email ?? "";
        $log = explode("EXH_", $data->log);
        $code =  $data->log;
        $rows_count =  $data->count;

        $text = "\nFinalización de carga masiva de clientes $code";

        $myfile = file_put_contents(storage_path() . '/app/public/import_logs/' . $code . '.log', $text . PHP_EOL, FILE_APPEND | LOCK_EX);

        Import::UpdateOrCreate(['code' => $code], [
            "model" => "Clientes",
            "code" => $code,
            "rows" => $rows_count,
            "advance" => $rows_count,
            "admin_id" => $data->auth_user->id ?? 99,
            "status" => "FINALIZADO"
        ]);

        // Mail::to($email)->queue(new notificationFinishImport($email, $log[1], date("d-m-Y H:i:s", strtotime($date_start)), $code));
    }
}
