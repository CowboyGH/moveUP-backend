<!DOCTYPE html>
<html>
<head>
    <title>Подтверждение email</title>
    <style>
        body { font-family: Arial, sans-serif;}
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; }
        .code { font-size: 24px; font-weight: bold; color: #3490dc; padding: 10px; background-color: #f0f7ff; border-radius: 4px; text-align: center; margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Подтверждение email адреса</h2>
    <p>Здравствуйте!</p>
    <p>Благодарим за регистрацию в moveUP!</p>
    <p>Для завершения регистрации подтвердите свой email адрес, используя следующий код:</p>
    <div class="code">{{ $code }}</div>
    <p><strong>Внимание:</strong> Этот код действителен в течение <strong>5 минут</strong>.</p>
    <p>Если вы не регистрировались на нашем сайте, просто проигнорируйте это письмо.</p>

    <div class="footer">
        <p>С уважением,<br>Команда moveUP</p>
        <p>Если у вас возникли трудности, пожалуйста, свяжитесь с нашей службой поддержки.</p>
    </div>
</div>
</body>
</html>
