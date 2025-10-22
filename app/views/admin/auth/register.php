<div class="card overflow-hidden">
    <div class="bg-primary">
        <div class="text-primary text-center p-4">
            <h5 class="text-white font-size-20">Free Register</h5>
            <p class="text-white-50">Get your Advanced CMS account now.</p>
            <a href="/" class="logo logo-admin">
                <img src="/assets/images/logo-sm.png" height="24" alt="logo">
            </a>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="p-3">
            <form class="mt-4" action="/admin/register" method="POST">
                
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password_confirm">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm password" required>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">Register</button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="mt-5 text-center">
    <p>Already have an account? <a href="/admin/login" class="fw-medium text-primary"> Login </a></p>
</div>
