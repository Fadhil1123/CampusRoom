<!DOCTYPE html>
<html>
<head>
    <title>Login CampusRoom</title>
</head>
<body>

    <h2>Login CampusRoom</h2>

    @if(session('error'))
        <p>{{ session('error') }}</p>
    @endif

    <form action="/login" method="POST">

        @csrf

        <input type="text"
            name="nim_nip"
            placeholder="NIM / NIP">

        <br><br>

        <input type="password"
            name="password"
            placeholder="Password">

        <br><br>

        <button type="submit">
            Login
        </button>

    </form>

</body>
</html>