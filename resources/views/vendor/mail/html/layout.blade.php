<html>

<head>
  <title>Welcome to Dantown</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <style type="text/css">
    /* FONTS */
    @media screen {
      @font-face {
        font-family: "Lato";
        font-style: normal;
        font-weight: 400;
        src: local("Lato Regular"), local("Lato-Regular"),
          url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format("woff");
      }

      @font-face {
        font-family: "Lato";
        font-style: normal;
        font-weight: 700;
        src: local("Lato Bold"), local("Lato-Bold"),
          url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format("woff");
      }

      @font-face {
        font-family: "Lato";
        font-style: italic;
        font-weight: 400;
        src: local("Lato Italic"), local("Lato-Italic"),
          url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format("woff");
      }

      @font-face {
        font-family: "Lato";
        font-style: italic;
        font-weight: 700;
        src: local("Lato Bold Italic"), local("Lato-BoldItalic"),
          url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format("woff");
      }
    }

    /* CLIENT-SPECIFIC STYLES */
    body,
    table,
    td,
    a {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table,
    td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      -ms-interpolation-mode: bicubic;
    }

    /* RESET STYLES */
    img {
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
    }

    table {
      border-collapse: collapse !important;
    }

    body {
      height: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
    }

    /* iOS BLUE LINKS */
    a[x-apple-data-detectors] {
      color: inherit !important;
      text-decoration: none !important;
      font-size: inherit !important;
      font-family: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
    }

    /* ANDROID CENTER FIX */
    div[style*="margin: 16px 0;"] {
      margin: 0 !important;
    }
  </style>
</head>

<body style=" background-color: #fff7ea; margin: 0 !important; padding: 0 !important; ">
     <!-- HIDDEN PREHEADER TEXT -->
  <div style=" display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; ">

  </div>


  <table border="0"  cellpadding="0" cellspacing="0" width="100%">
    <!-- LOGO -->
    <tr>
        <td bgcolor="" align="center" >
          <table border="0" cellpadding="0"  cellspacing="0" width="480">
            <tr>
              <td align="center" width="480" valign="top" style="
                  background-color: #fff7ea;
                  padding: 30px;
                  border-radius:0px;
                  height:20px;
                  margin-top:0px;
                  ">
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td bgcolor="" align="center" >
          <table border="0" cellpadding="0"  cellspacing="0" width="480">
            <tr>
              <td align="center" width="480" valign="top" style="
                  background-color: #ffffff;
                  padding: 10px;
                  border-radius:0px;
                  height:10px;
                  margin-top:0px;
                  ">
              </td>
            </tr>
          </table>
        </td>
      </tr>

    <tr>
      <td bgcolor="" align="center" >
        <table border="0" cellpadding="0"  cellspacing="0" width="480">
          <tr>
            <td align="center" width="400" valign="top" style="
                background-color: #ffffff;
                padding: 25px;
                margin-top:-30px;
                ">
              <a href="#" target="_blank">
                <img src="{{url('images/email_logo.png')}}" width="480" height="300" style="
                      display: block;
                      font-family: 'Lato', Helvetica, Arial, sans-serif;
                      color: #ffffff;
                      font-size: 18px;
                      background-color:none;
                    " border="0" />
              </a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <!-- HERO -->
    {{ $header ?? '' }}
    <!-- COPY BLOCK -->
    <tr>
      <td bgcolor="" align="center" style="padding: 0px 10px 0px 10px">
        <table border="0" cellpadding="0" cellspacing="0" width="480">
          <!-- COPY -->
          <tr>
            <td bgcolor="#FFFCF7" align="left" style="
                  padding: 20px 30px 40px 30px;
                  color: #666666;
                  font-family: 'Lato', Helvetica, Arial, sans-serif;
                  font-size: 18px;
                  font-weight: 400;
                  line-height: 25px;
                  margin-top: -300px;
                  background-color: #ffffff;
                ">
                <br><br>
              <p style="margin: 0">

                {{ Illuminate\Mail\Markdown::parse($slot) }}

              </p>
              <br>
              <p>
                <span>Warm Regards</span><br>
                <span style=" font-weight:bolder">Dantown Team</span>
            </p>
            <br><br><br>
            </td>

          </tr>

          <tr>
            <td bgcolor="#ffffff" align="center" style="padding:0px">
                <table border="0" cellpadding="0" cellspacing="0" width="450">
                  <!-- HEADLINE -->
                  <tr>
                    <td bgcolor="" align="center" style="

                            font-family: 'Lato', Helvetica, Arial, sans-serif;
                            background-image: url('{{url('/images/footer_img.png')}}');
                            background-size:cover;
                            height:120px;
                          ">

                    </td>

                    <tr>
                        <td>
                            <div style="display: flex; justify-content:center">
                                <a href="https://play.google.com/store/apps/details?id=com.dantown.Dantownapp">
                                    <img src="{{url('images/GOOGLE_PLAY.png')}}" style="width: 115px; margin:10px" alt="">
                                </a>
                                <a href="https://apps.apple.com/US/app/id1575600937?mt=8">
                                    <img src="{{url('images/APPSTORE.png')}}" style="width: 115px; margin:10px" alt="">
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <hr style="height:3px; background-color: #000070">
                        </td>
                    </tr>
                  </tr>
                </table>
              </td>
          </tr>

          <tr>
            <td bgcolor="#ffffff" align="center" style="padding: 0px; ">
                <table border="0" cellpadding="0" cellspacing="0" width="450">
                  <!-- HEADLINE -->
                  <tr>
                    <td bgcolor="#00B9CD" align="center" style="
                            padding: 20px;
                            font-family: 'Lato', Helvetica, Arial, sans-serif;
                            font-size: 18px;
                            font-weight: 400;
                            line-height: 25px;
                          ">
                            <div style="display: flex; justify-content:center">
                                <a href="http://www.facebook.com/godantown">
                                    <img src="{{url('images/facebook.png')}}" style="height: 13px; margin:10px" alt="">
                                </a>
                                <a href="https://twitter.com/godantown">
                                    <img src="{{url('images/twitter.png')}}" style="height: 13px; margin:10px" alt="">
                                </a>
                                <a href="https://instagram.com/godantown">
                                    <img src="{{url('images/instagram.png')}}" style="height: 13px; margin:10px" alt="">
                                </a>
                                <a href="https://www.linkedin.com/company/dantown">
                                    <img src="{{url('images/linkedin.png')}}" style="height: 13px; margin:10px" alt="">
                                </a>

                            </div>
                      <h2 style="
                              font-size: 14px;
                              font-weight: 400;
                              color: #000070;
                              margin: auto;
                            "> No 152 NTA/Ozouba Road Port Harcourt Rivers State NG</h2>
                      {{-- <p style="margin: 0">
                        <a href="#" target="_blank" style="color: #e4e3e3">We &rsquo; re here, ready to talk</a>
                      </p> --}}
                    </td>
                  </tr>
                </table>
              </td>
          </tr>
          <tr style="height: 40px">
              <td>

              </td>
          </tr>

        </table>
      </td>
    </tr>
    <!-- COPY CALLOUT -->



    {{-- <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px">
      <table border="0" cellpadding="0" cellspacing="0" width="480">
        <!-- HEADLINE -->
        <tr>
          <td bgcolor="#00B9CD" align="center" style="
                  padding: 20px;
                  border-radius: 4px 4px 4px 4px;
                  color: #e7a20d;
                  font-family: 'Lato', Helvetica, Arial, sans-serif;
                  font-size: 18px;
                  font-weight: 400;
                  line-height: 25px;
                ">
                  <img src="images/socail_icon.png" alt="">
            <h2 style="
                    font-size: 14px;
                    font-weight: 400;
                    color: #000070;
                    margin: auto;
                  ">Office 6, No 152 NTA/Ozouba Road Port Harcourt Rivers State NG</h2>
            <p style="margin: 0">
              <a href="#" target="_blank" style="color: #e4e3e3">We &rsquo; re here, ready to talk</a>
            </p>
          </td>
        </tr>
      </table>
    </td> --}}
    </tr>
    <!-- FOOTER -->
    {{ $footer ?? '' }}
  </table>
</body>
</html>
