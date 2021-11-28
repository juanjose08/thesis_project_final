<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Megason Diagnostic Clinic</title>
        <link href="{{asset('css/styles.css')}}" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        @include('sweetalert::alert')
        <div id="layoutAuthentication" style="background-color: #32CD32!important;">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container" style="margin-top: 45px!important;">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <img src="{{asset('assets/img/logo.png')}}" alt="" class="img-responsive">
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('validate-otp') }}" aria-label="{{ __('Enter OTP') }}">
                                            @csrf
                    
                                            <div class="form-group">
                                                <label class="small mb-1" for="otp">Enter OTP:</label>
                                                <input class="form-control py-4" name="otp" required autofocus id="otp" type="text" placeholder="Enter OTP" />
                                            </div>
                                            
                                            <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                                
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="small"><a href="{{ route('new-otp') }}">Request new OTP</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Megason Diagnostic Clinic 2021</div>
                            <div>
                                {{-- <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a> --}}
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
    </body>
</html>