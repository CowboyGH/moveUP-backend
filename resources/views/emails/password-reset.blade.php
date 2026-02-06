<!DOCTYPE html>
<html>
<head>
    <title>Восстановление пароля</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; }
        .code { font-size: 24px; font-weight: bold; color: #3490dc; padding: 10px; background-color: #f0f7ff; border-radius: 4px; text-align: center; margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Восстановление пароля</h2>
    <p>Здравствуйте!</p>
    <p>Для восстановления пароля используйте следующий код подтверждения:</p>
    <div class="code">{{ $code }}</div>
    <p><strong>Внимание:</strong> Этот код действителен в течение <strong>5 минут</strong>.</p>
    <p>Если вы не запрашивали восстановление пароля, просто проигнорируйте это письмо.</p>

    <div class="footer">
        <p>С уважением,<br>Команда moveUP</p>
    </div>
</div>
</body>
</html>
