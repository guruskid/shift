<!DOCTYPE html>
<html>

<head>
    <title>Disbled Account</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
</head>



<div class="Container" style="margin: 15%;">
    <div class="card">
        <div class="row">
            <div class="col-md-3" style="margin-top: 5%; margin-bottom: 5%; margin-left: 5%; color: #000070;">
                <svg width="80%" height="80%" viewBox="0 0 16 16" class="bi bi-person-square" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                    <path fill-rule="evenodd" d="M2 15v-1c0-1 1-4 6-4s6 3 6 4v1H2zm6-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                </svg>
            </div>
            <div class="col-md-6" style="margin-top: 5%; margin-bottom: 5%;">
                <strong>
                    <h3 class="text-dark">Hello {{Auth::user()->first_name ?? 'there'}} </h3>
                </strong>
                <p class="text-muted text-left ">
                    Your account has been deactivated, and hence you cannnot Trade. To Trade ask the Admin or Senior
                    Accountant to Activate your Account. Reload the page when your account has been activated.
                </p>


                <form action="{{route('logout')}}" method="POST" class="form-inline" id="logout">@csrf
                    <a href="/home">
                        <button type="button" class="btn btn-secondary btn-rounded">
                            Reload
                        </button>
                    </a>
                    @auth
                    <button class="ml-2 btn btn-outline-secondary btn-rounded">
                        Logout
                    </button>
                    @endauth
                    @guest
                    <a href="{{route('login')}}">Login</a>
                    @endguest

                </form>

            </div>
        </div>
    </div>

</div>




</html>
