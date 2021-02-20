<!DOCTYPE html>

<body>
<div>
    <h1> Welcome to MUKA</h1>

    Verify: {{ env('APP_URL') . '/' . $notifiable->verify_token }}
</div>

</body>
</html>