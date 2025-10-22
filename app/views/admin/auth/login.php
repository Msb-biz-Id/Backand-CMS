<div class="card overflow-hidden">
    <div class="bg-primary">
        <div class="text-primary text-center p-4">
            <h5 class="text-white font-size-20">Welcome Back!</h5>
            <p class="text-white-50">Sign in to Advanced CMS</p>
            <a href="/" class="logo logo-admin">
                <img src="/assets/images/logo-sm.png" height="24" alt="logo">
            </a>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="p-3">
            <form class="mt-4" action="/admin/login" method="POST">
                
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>

                <div class="mb-3 row">
                    <div class="col-sm-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>
                    <div class="col-sm-6 text-end">
                        <a href="/admin/forgot-password" class="text-muted"><i class="mdi mdi-lock"></i> Forgot password?</a>
                    </div>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="mt-5 text-center">
    <p>Don't have an account? <a href="/admin/register" class="fw-medium text-primary"> Register </a></p>
    <p class="text-muted">Default Login: <strong>admin@example.com</strong> / <strong>admin123</strong></p>
</div>
