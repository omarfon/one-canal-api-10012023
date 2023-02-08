<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\SalaryAdvance;
use Illuminate\Bus\Queueable;
use App\Helpers\SalaryAdvanceHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $class;
    protected $email;
    protected $details;

    public function __construct($email, $details, $class)
    {
        $this->email = $email;
        $this->details = $details;
        $this->class = $class;
    }

    public function handle()
    {
        $email_class = "App\\Mail\\SendMail";
        $filesAttachment = [];

        switch ($this->class) {
            case 'SendMailNewAdministrator':
                $view = "emails.newAdministrator";
                $subject = "Se le ha creado una cuenta como administrador de la aplicación One Canal";
                break;

            case 'SendMailClientActivate':
                $view = "emails.validateClient";
                $subject = "Activación de cuenta en la aplicación One Canal";
                break;

            case 'SendMailClientCodeActivate':
                $view = "emails.codeValidateClient";
                $subject = "Activación de cuenta en la aplicación One Canal";
                break;

            case 'SendMailSuccessClientActivate':
                $filesAttachment[] = SalaryAdvanceHelper::generatePdf($this->details, "terms");
                $filesAttachment[] = SalaryAdvanceHelper::generatePdf($this->details, "contract");

                $view = "emails.successValidateClient";
                $subject = "Ha validado su cuenta correctamente";
                break;

            case 'SendMailClientForgotPassword':
                $view = "emails.forgotPasswordClient";
                $subject = "Recuperación de contraseña de cuenta en la aplicación One Canal";
                break;

            case 'SendMailClientCodeForgotPassword':
                $view = "emails.codeForgotPasswordClient";
                $subject = "Recuperación de contraseña de cuenta en la aplicación One Canal";
                break;

            case 'SendMailNewSalaryAdvance':
                $salaryAdvance = SalaryAdvance::whereId($this->details['salaryAdvance']->id)->with('user', 'account.bank', 'reason')->first();

                $this->details = $salaryAdvance;

                $filesAttachment[] = SalaryAdvanceHelper::generatePdf($this->details['user'], "advance", "pdf", $salaryAdvance);

                $view = "emails.newSalaryAdvance";
                $subject = "Se ha registrado la solicitud de adelanto de sueldo el " . date("d-m-Y", strtotime($this->details->created_at));
                break;

            case 'SendMailAdminNewSalaryAdvance':
                $this->details = SalaryAdvance::whereId($this->details->id)->with('user', 'account.bank', 'reason')->first();

                $view = "emails.newSalaryAdvanceToAdmin";
                $subject = "Se ha registrado una nueva solicitud de adelanto de sueldo el " . date("d-m-Y", strtotime($this->details->created_at));
                break;

            case 'SendMailAdminNewUserAccount':
                $this->details = Account::whereId($this->details->id)->with('user', 'bank')->first();

                $view = "emails.newAccountToAdmin";
                $subject = "Se ha registrado una nueva cuenta por parte de un cliente";
                break;

            case 'SendMailClientApprovedAccount':
                $this->details = Account::whereId($this->details->id)->with('user', 'bank')->first();

                $view = "emails.approvedAccountToClient";
                $subject = "Se ha aprobado una cuenta registrada por usted";
                break;

            case 'SendMailChangeStatusSalaryAdvance':
                $this->details = SalaryAdvance::whereId($this->details->id)->with('user', 'account.bank', 'reason')->first();

                switch ($this->details->status) {
                    case 'CONFIRMED':
                        $this->details->status = "confirmada";
                        $this->details->status_description = "Hemos comprobado que los datos ingresados son correctos y usted cumple con los requisitos para este beneficio.";
                        break;

                    case 'DEPOSITED':
                        $this->details->status = "abonada";
                        $this->details->status_description = "Hemos depositado el dinero en su cuenta. Recuerde que el mismo será descontado en el pago de su salario.";
                        break;

                    case 'COMPLETED':
                        $this->details->status = "finalizada";
                        $this->details->status_description = "Ya el dinero del beneficio fue descontado de su salario y ahora ya puede solicitar un nuevo adelanto.";
                        break;

                    default:
                        # code...
                        break;
                }

                $view = "emails.changeStatusSalaryAdvance";
                $subject = "Cambio de estado en adelanto de sueldo el " . date("d-m-Y", strtotime($this->details->created_at));
                break;

            case 'SendMailRefuseSalaryAdvance':
                $this->details = SalaryAdvance::whereId($this->details->id)->with('user', 'account.bank', 'reason')->first();

                $view = "emails.refuseSalaryAdvance";
                $subject = "Su solicitud de adelanto de sueldo el " . date("d-m-Y", strtotime($this->details->created_at)) . " ha sido rechazada";
                break;

            case 'SendMailAdminForgotPassword':
                $view = "emails.forgotPasswordAdmin";
                $subject = "Recuperación de contraseña de cuenta en la aplicación One Canal Back Office";
                break;

            default:
                return true;
                break;
        }

        Mail::to($this->email)
            ->send(new $email_class($this->details, $subject, $view, $filesAttachment));
    }
}
