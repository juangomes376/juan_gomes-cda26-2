    <nav>
        <a href="/cash2/index.php">Home</a>
        <a href="/cash2/private.php">Private Page</a>
        <?php if (isset($_SESSION['user_name'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
            <a href="/cash2/assets/scripts/logout.php">Logout</a>
        <?php endif; ?>
        <?php if (!isset($_SESSION['user_name'])): ?>
            <a href="/cash2/login.php">Login</a>
            <a href="/cash2/register.php">Register</a>
        <?php endif; ?>
    </nav>