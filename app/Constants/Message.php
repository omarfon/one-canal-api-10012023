<?php

namespace App\Constants;

class Message
{
    const USER_NOT_FOUND = "Sus datos son incorrectos, inténtelo nuevamente";
    const USER_NOT_ACTIVATED = "Su cuenta no está activada";
    const USER_ATTEMPTS_LIMIT = "Alcanzó el límite de intentos incorrectos, por favor, vuelva a intentarlo en 24 horas";
    const USER_BLOCKED = "Su cuenta está bloqueada";

    const USER_NOT_FOUND_FOR_ACTIVATED = "Aún no se encuentra registrado en nuestra plataforma";
    const SEND_MAIL_ACTIVATED = "Se ha enviado un correo electrónico para la activación de su cuenta";
    const USER_ACTIVATED = "Esta cuenta ya se encuentra activada, por favor, inicie sesión con sus credenciales";
    const DONT_SEND_EMAIL_VALIDATE_RESET = "Ya tiene una solicitud de activación pendiente. Podrá solicitar un nuevo correo luego de haber pasado 15 minutos de la solicitud";
    const DONT_SEND_EMAIL_PASSWORD_RESET = "Ya tiene una solicitud de recuperación de contraseña pendiente. Podrá solicitar un nuevo correo luego de haber pasado 15 minutos de la solicitud";

    const TOKEN_NOT_FOUND = "Código inválido";
    const TOKEN_EXPIRED = "Código expirado";
    const TOKEN_VALIDATE = "Código válido";

    const ACCOUNT_VALIDATED = "La activación de su cuenta fue exitosa";

    const DATA_NOT_CONFIRMED = "Al parecer, sus datos no coinciden";

    const SEND_MAIL_FORGOT_PASSWORD = "Se ha enviado un correo electrónico para cambiar la contraseña";
    const RECOVER_PASSWORD = "Su nueva contraseña se ha guardado exitosamente";

    const LOGOUT = "Sesión cerrada correctamente";

    const OUT_OF_PERIOD = "No se pueden solicitar adelantos de sueldo ahora, inténtelo al inicio del siguiente mes, por favor.";
    const LIMIT_OF_PERIOD = "Ya tiene una solicitud en proceso";

    const SALARY_ADVANCE_REQUEST_ERROR = "Error al solicitar adelanto de sueldo. Por favor, inténtelo nuevamente";

    // Business Fees Ranges

    const INVALID_MIN_LIMIT = "El límite mínimo es inválido, no existe otro rango que termine en este límite mínimo.";
    const INVALID_LIMITS = "Los límites son incorrectos. Ya hay otro rango dentro estos límites.";
    const INVALID_0_LIMIT = "El límite inicial debe ser igual a 0";
    const INVALID_MAX_LIMIT = "No puede eliminar este rango porque no es el límite mayor";

    const ADMIN_NOT_FOUND = "Correo electrónico no registrado";
}
