<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!--favicon-->
  <link rel="icon" href="{{ asset('assets/images/icon.png') }}" type="image/png" />
  <!--plugins-->
  <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/smart-wizard/css/smart_wizard_all.min.css') }}" rel="stylesheet" type="text/css" />

  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" /> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.18/css/intlTelInput.css" integrity="sha512-gxWow8Mo6q6pLa1XH/CcH8JyiSDEtiwJV78E+D+QP0EVasFs8wKXq16G8CLD4CJ2SnonHr4Lm/yY2fSI2+cbmw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- loader-->
  <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
  <script src="{{ asset('assets/js/pace.min.js') }}"></script>
  <!-- Bootstrap CSS -->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
  <title>SmartMauzo - Authentication</title>
</head>

<body class="bg-login">
  <!--wrapper-->
  <div class="wrapper">
    <header class="login-header shadow">
      <nav class="navbar navbar-expand-lg navbar-light bg-white rounded fixed-top rounded-0 shadow-sm navtest">
        <div class="container-fluid">
          <a class="navbar-brand" href="{{url('/') }}">
            <img src="{{ asset('assets/images/icon.png') }}" width="40" height="40" alt="" /> SmartMauzo
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent1">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              <li class="nav-item"> <a class="nav-link" href="{{ url('/') }}"><i class='bx bx-user me-1'></i>About</a>
              </li>
              <li class="nav-item"> <a class="nav-link" href="#"><i class='bx bx-category-alt me-1'></i>Features</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="{{ url('how-to-pay')}}"><i class="bx bx-help-circle"></i> How To Pay</a></li>
              <li class="nav-item"> <a class="nav-link" href="callto::+255753477470"><i class='bx bx-phone me-1'></i>Call Us: +255 753 477 470</a>
              </li>
              <li class="nav-item">
                @if(app()->getLocale() == 'en')
                  <a class="nav-link" href="{{url('switchlang/sw')}}"><i class="bx bx-globe"></i> SW</a>
                @else
                  <a class="nav-link" href="{{url('switchlang/en')}}"><i class="bx bx-globe"></i> EN</a>
                @endif
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    @include('flash-message')
    @yield('content')
    <footer class="bg-white shadow-sm border-top p-2 text-center fixed-bottom">
      <p class="mb-0">Copyright © <script>document.write(new Date().getFullYear());</script> <a href="https://ovaltechtz.com/">Oval Tech (T) Ltd</a>.</strong>. All right reserved.</p>
    </footer>
  </div>
  <!--end wrapper-->
  <!-- Bootstrap JS -->
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <!--plugins-->
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/plugins/smart-wizard/js/jquery.smartWizard.min.js') }}"></script>

  <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.18/js/intlTelInput-jquery.min.js" integrity="sha512-Bc9LELGc9+AYg+/BYBR9O8OEc3JrwZi8OHFwdTqYeYymMUpexQLkp01q/xTMyS3aE8TLdMSV1W3at2pBo4BlSA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- InputMask -->
  <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.js') }}"></script>
  <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>    
  <script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
  <script>
    $(document).ready(function () {
      var code = "+255"; // Assigning value from model.
      $('#inputPhoneNumber').val(code);
      $('#inputPhoneNumber').intlTelInput({
        autoHideDialCode: true,
        autoPlaceholder: "ON",
        dropdownContainer: document.body,
        formatOnDisplay: true,
        hiddenInput: "full_number",
        initialCountry: "auto",
        nationalMode: true,
        placeholderNumberType: "MOBILE",
        preferredCountries: ['TZ'],
        separateDialCode: true
      });
      // Toolbar extra buttons
      var btnFinish = $('<button></button>').text('Finish').addClass('btn btn-info').on('click', function () {
        // alert('Finish Clicked');
        'use strict'
  
        var iso2 = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").iso2;
        var dialCode = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").dialCode;
        var phoneNumber = $('#inputPhoneNumber').val();
        var name = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").name;
        // alert('Country Code : ' + code + '\nPhone Number : ' + phoneNumber + '\nCountry Name : ' + name);
        var cc = $('#countryCode').val(iso2.toUpperCase());
        var dc = $('#dialCode').val(dialCode);
        var country = $('#country').val(name);
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')
  
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
  
          form.classList.add('was-validated')
          }, false)
        })
        
      });
      var btnCancel = $('<button></button>').text('Cancel').addClass('btn btn-danger').on('click', function () {
        $('#smartwizard').smartWizard("reset");
      });
      // Step show event
      $("#smartwizard").on("showStep", function (e, anchorObject, stepNumber, stepDirection, stepPosition) {
        $("#prev-btn").removeClass('disabled');
        $("#next-btn").removeClass('disabled');
        if (stepPosition === 'first') {
          $("#prev-btn").addClass('disabled');
        } else if (stepPosition === 'last') {
          $("#next-btn").addClass('disabled');
        } else {
          $("#prev-btn").removeClass('disabled');
          $("#next-btn").removeClass('disabled');
        }
      });
      // Smart Wizard
      $('#smartwizard').smartWizard({
        selected: 0,
        theme: 'dots',
        transition: {
          animation: 'slide-horizontal', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
        },
        toolbarSettings: {
          toolbarPosition: 'bottom', // both bottom
          toolbarExtraButtons: [btnFinish]
        }
      });
      // External Button Events
      $("#reset-btn").on("click", function () {
        // Reset wizard
        $('#smartwizard').smartWizard("reset");
        return true;
      });
      $("#prev-btn").on("click", function () {
        // Navigate previous
        $('#smartwizard').smartWizard("prev");
        return true;
      });
      $("#next-btn").on("click", function () {
        // Navigate next
        $('#smartwizard').smartWizard("next");
        return true;
      });
      // Demo Button Events
      $("#got_to_step").on("change", function () {
        // Go to step
        var step_index = $(this).val() - 1;
        $('#smartwizard').smartWizard("goToStep", step_index);
        return true;
      });
      $("#is_justified").on("click", function () {
        // Change Justify
        var options = {
          justified: $(this).prop("checked")
        };
        $('#smartwizard').smartWizard("setOptions", options);
        return true;
      });
      $("#animation").on("change", function () {
        // Change theme
        var options = {
          transition: {
            animation: $(this).val()
          },
        };
        $('#smartwizard').smartWizard("setOptions", options);
        return true;
      });
      $("#theme_selector").on("change", function () {
        // Change theme
        var options = {
          theme: $(this).val()
        };
        $('#smartwizard').smartWizard("setOptions", options);
        return true;
      });


      $('[data-mask]').inputmask();
    });
  </script>
  <!--Password show & hide js -->
  <script>
    $(document).ready(function () {
      $("#show_hide_password a").on('click', function (event) {
        event.preventDefault();
        if ($('#show_hide_password input').attr("type") == "text") {
          $('#show_hide_password input').attr('type', 'password');
          $('#show_hide_password i').addClass("bx-hide");
          $('#show_hide_password i').removeClass("bx-show");
        } else if ($('#show_hide_password input').attr("type") == "password") {
          $('#show_hide_password input').attr('type', 'text');
          $('#show_hide_password i').removeClass("bx-hide");
          $('#show_hide_password i').addClass("bx-show");
        }
      });

      $("#btype").on('change', function(){
            var typeid = $(this).val(); // this.value
            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
            $.ajax({ 
                url: "{{ url('get-sub-types')}}",
                data: { business_type_id: typeid },
                type: 'post'
            }).done(function(data) {
                console.log('Done: ', data);
                var sel = $("#sub-type");
                sel.empty();
                for (var i=0; i<data.length; i++) {
                  sel.append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
                }
                sel.append('<option value"">Others</option>');
            }).fail(function() {
                console.log('Failed');
            });
        });

      
      $('input[type="radio"]').on('click', function(event){
          if ($(this).val() == "yes") {
            document.getElementById('ifYes').style.display = 'block';
          }else{
            document.getElementById('ifYes').style.display = 'none';
          }
          // alert($(this).val()); // alert value
      });
    }); 

    // Example starter JavaScript for disabling form submissions if there are invalid fields
      (function () {
        'use strict'
  
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')
  
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
  
          form.classList.add('was-validated')
          }, false)
        })
      })()
  </script>
  <!--app JS-->
  <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>