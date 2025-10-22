<!-- Contact Page (Dynamic Form) -->
<div class="hero-section" style="padding: 3rem 0;">
    <div class="container text-center">
        <h1 class="fw-bold mb-3">Contact Us</h1>
        <p class="lead text-muted">We'd love to hear from you. Send us a message!</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="/contact" id="contact-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="ri-send-plane-line me-2"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-map-pin-line me-2"></i> Our Office
                        </h5>
                        <p class="mb-2">
                            <strong>Address:</strong><br>
                            Jl. Example No. 123<br>
                            Jakarta, Indonesia 12345
                        </p>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-phone-line me-2"></i> Contact Info
                        </h5>
                        <p class="mb-2">
                            <strong>Phone:</strong><br>
                            +62 812-3456-7890
                        </p>
                        <p class="mb-2">
                            <strong>Email:</strong><br>
                            info@example.com
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-time-line me-2"></i> Business Hours
                        </h5>
                        <p class="mb-1">
                            <strong>Monday - Friday:</strong><br>
                            9:00 AM - 5:00 PM
                        </p>
                        <p class="mb-0">
                            <strong>Weekend:</strong><br>
                            Closed
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
