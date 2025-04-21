<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form method="POST" action="{{ route('fobi.register') }}">
        @csrf
        <div>
            <label for="fname">First Name:</label>
            <input type="text" name="fname" id="fname" required>
        </div>
        <div>
            <label for="lname">Last Name:</label>
            <input type="text" name="lname" id="lname" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="uname">Username:</label>
            <input type="text" name="uname" id="uname" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" required>
        </div>
        <div>
            <label for="organization">Organization:</label>
            <input type="text" name="organization" id="organization" required>
        </div>
        <div>
            <label for="link_burungnesia">Tautkan dengan Burungnesia</label>
            <input type="checkbox" name="link_burungnesia" id="link_burungnesia" value="1">
        </div>
        <button type="submit">Register</button>
    </form>
</body>
</html>
