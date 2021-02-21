<!DOCTYPE html>

<body>
<div>
    <h1> Reset your password at muka </h1>

    Reset: {{url('/password/reset/' . $token)}}
</div>

</body>
</html>