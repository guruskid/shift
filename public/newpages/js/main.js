const data = {
    usd: {
        key: "usd",
        country: "usa",
        physical: [{ id: 1, price: "$25", ppc: 1500 }]
        // ecode: [
        //     { id: 1, min: "31", max: "40", rate: "490/$" },
        //     { id: 2, min: "41", max: "50", rate: "760/$" },
        //     { id: 3, min: "51", max: "60", rate: "910/$" },
        // ],
        // large: [
        //     { id: 100, min: "31", max: "40", rate: "490/$" },
        //     { id: 122, min: "41", max: "50", rate: "760/$" },
        //     { id: 343, min: "51", max: "60", rate: "910/$" },
        // ],
        // small: [
        //     { id: 1, min: "31", max: "40", rate: "490/$" },
        //     { id: 2, min: "41", max: "50", rate: "760/$" },
        //     { id: 3, min: "51", max: "60", rate: "910/$" },
        // ],
    }
};

// const countries_iso = [
//     {
//         name: "Afghanistan",
//         flag: "🇦🇫",
//         code: "AF",
//         dial_code: "+93"
//     },
//     {
//         name: "Åland Islands",
//         flag: "🇦🇽",
//         code: "AX",
//         dial_code: "+358"
//     },
//     {
//         name: "Albania",
//         flag: "🇦🇱",
//         code: "AL",
//         dial_code: "+355"
//     },
//     {
//         name: "Algeria",
//         flag: "🇩🇿",
//         code: "DZ",
//         dial_code: "+213"
//     },
//     {
//         name: "American Samoa",
//         flag: "🇦🇸",
//         code: "AS",
//         dial_code: "+1684"
//     },
//     {
//         name: "Andorra",
//         flag: "🇦🇩",
//         code: "AD",
//         dial_code: "+376"
//     },
//     {
//         name: "Angola",
//         flag: "🇦🇴",
//         code: "AO",
//         dial_code: "+244"
//     },
//     {
//         name: "Anguilla",
//         flag: "🇦🇮",
//         code: "AI",
//         dial_code: "+1264"
//     },
//     {
//         name: "Antarctica",
//         flag: "🇦🇶",
//         code: "AQ",
//         dial_code: "+672"
//     },
//     {
//         name: "Antigua and Barbuda",
//         flag: "🇦🇬",
//         code: "AG",
//         dial_code: "+1268"
//     },
//     {
//         name: "Argentina",
//         flag: "🇦🇷",
//         code: "AR",
//         dial_code: "+54"
//     },
//     {
//         name: "Armenia",
//         flag: "🇦🇲",
//         code: "AM",
//         dial_code: "+374"
//     },
//     {
//         name: "Aruba",
//         flag: "🇦🇼",
//         code: "AW",
//         dial_code: "+297"
//     },
//     {
//         name: "Australia",
//         flag: "🇦🇺",
//         code: "AU",
//         dial_code: "+61"
//     },
//     {
//         name: "Austria",
//         flag: "🇦🇹",
//         code: "AT",
//         dial_code: "+43"
//     },
//     {
//         name: "Azerbaijan",
//         flag: "🇦🇿",
//         code: "AZ",
//         dial_code: "+994"
//     },
//     {
//         name: "Bahamas",
//         flag: "🇧🇸",
//         code: "BS",
//         dial_code: "+1242"
//     },
//     {
//         name: "Bahrain",
//         flag: "🇧🇭",
//         code: "BH",
//         dial_code: "+973"
//     },
//     {
//         name: "Bangladesh",
//         flag: "🇧🇩",
//         code: "BD",
//         dial_code: "+880"
//     },
//     {
//         name: "Barbados",
//         flag: "🇧🇧",
//         code: "BB",
//         dial_code: "+1246"
//     },
//     {
//         name: "Belarus",
//         flag: "🇧🇾",
//         code: "BY",
//         dial_code: "+375"
//     },
//     {
//         name: "Belgium",
//         flag: "🇧🇪",
//         code: "BE",
//         dial_code: "+32"
//     },
//     {
//         name: "Belize",
//         flag: "🇧🇿",
//         code: "BZ",
//         dial_code: "+501"
//     },
//     {
//         name: "Benin",
//         flag: "🇧🇯",
//         code: "BJ",
//         dial_code: "+229"
//     },
//     {
//         name: "Bermuda",
//         flag: "🇧🇲",
//         code: "BM",
//         dial_code: "+1441"
//     },
//     {
//         name: "Bhutan",
//         flag: "🇧🇹",
//         code: "BT",
//         dial_code: "+975"
//     },
//     {
//         name: "Bolivia, Plurinational State of bolivia",
//         flag: "🇧🇴",
//         code: "BO",
//         dial_code: "+591"
//     },
//     {
//         name: "Bosnia and Herzegovina",
//         flag: "🇧🇦",
//         code: "BA",
//         dial_code: "+387"
//     },
//     {
//         name: "Botswana",
//         flag: "🇧🇼",
//         code: "BW",
//         dial_code: "+267"
//     },
//     {
//         name: "Bouvet Island",
//         flag: "🇧🇻",
//         code: "BV",
//         dial_code: "+47"
//     },
//     {
//         name: "Brazil",
//         flag: "🇧🇷",
//         code: "BR",
//         dial_code: "+55"
//     },
//     {
//         name: "British Indian Ocean Territory",
//         flag: "🇮🇴",
//         code: "IO",
//         dial_code: "+246"
//     },
//     {
//         name: "Brunei Darussalam",
//         flag: "🇧🇳",
//         code: "BN",
//         dial_code: "+673"
//     },
//     {
//         name: "Bulgaria",
//         flag: "🇧🇬",
//         code: "BG",
//         dial_code: "+359"
//     },
//     {
//         name: "Burkina Faso",
//         flag: "🇧🇫",
//         code: "BF",
//         dial_code: "+226"
//     },
//     {
//         name: "Burundi",
//         flag: "🇧🇮",
//         code: "BI",
//         dial_code: "+257"
//     },
//     {
//         name: "Cambodia",
//         flag: "🇰🇭",
//         code: "KH",
//         dial_code: "+855"
//     },
//     {
//         name: "Cameroon",
//         flag: "🇨🇲",
//         code: "CM",
//         dial_code: "+237"
//     },
//     {
//         name: "Canada",
//         flag: "🇨🇦",
//         code: "CA",
//         dial_code: "+1"
//     },
//     {
//         name: "Cape Verde",
//         flag: "🇨🇻",
//         code: "CV",
//         dial_code: "+238"
//     },
//     {
//         name: "Cayman Islands",
//         flag: "🇰🇾",
//         code: "KY",
//         dial_code: "+345"
//     },
//     {
//         name: "Central African Republic",
//         flag: "🇨🇫",
//         code: "CF",
//         dial_code: "+236"
//     },
//     {
//         name: "Chad",
//         flag: "🇹🇩",
//         code: "TD",
//         dial_code: "+235"
//     },
//     {
//         name: "Chile",
//         flag: "🇨🇱",
//         code: "CL",
//         dial_code: "+56"
//     },
//     {
//         name: "China",
//         flag: "🇨🇳",
//         code: "CN",
//         dial_code: "+86"
//     },
//     {
//         name: "Christmas Island",
//         flag: "🇨🇽",
//         code: "CX",
//         dial_code: "+61"
//     },
//     {
//         name: "Cocos (Keeling) Islands",
//         flag: "🇨🇨",
//         code: "CC",
//         dial_code: "+61"
//     },
//     {
//         name: "Colombia",
//         flag: "🇨🇴",
//         code: "CO",
//         dial_code: "+57"
//     },
//     {
//         name: "Comoros",
//         flag: "🇰🇲",
//         code: "KM",
//         dial_code: "+269"
//     },
//     {
//         name: "Congo",
//         flag: "🇨🇬",
//         code: "CG",
//         dial_code: "+242"
//     },
//     {
//         name: "Congo, The Democratic Republic of the Congo",
//         flag: "🇨🇩",
//         code: "CD",
//         dial_code: "+243"
//     },
//     {
//         name: "Cook Islands",
//         flag: "🇨🇰",
//         code: "CK",
//         dial_code: "+682"
//     },
//     {
//         name: "Costa Rica",
//         flag: "🇨🇷",
//         code: "CR",
//         dial_code: "+506"
//     },
//     {
//         name: "Cote d'Ivoire",
//         flag: "🇨🇮",
//         code: "CI",
//         dial_code: "+225"
//     },
//     {
//         name: "Croatia",
//         flag: "🇭🇷",
//         code: "HR",
//         dial_code: "+385"
//     },
//     {
//         name: "Cuba",
//         flag: "🇨🇺",
//         code: "CU",
//         dial_code: "+53"
//     },
//     {
//         name: "Cyprus",
//         flag: "🇨🇾",
//         code: "CY",
//         dial_code: "+357"
//     },
//     {
//         name: "Czech Republic",
//         flag: "🇨🇿",
//         code: "CZ",
//         dial_code: "+420"
//     },
//     {
//         name: "Denmark",
//         flag: "🇩🇰",
//         code: "DK",
//         dial_code: "+45"
//     },
//     {
//         name: "Djibouti",
//         flag: "🇩🇯",
//         code: "DJ",
//         dial_code: "+253"
//     },
//     {
//         name: "Dominica",
//         flag: "🇩🇲",
//         code: "DM",
//         dial_code: "+1767"
//     },
//     {
//         name: "Dominican Republic",
//         flag: "🇩🇴",
//         code: "DO",
//         dial_code: "+1849"
//     },
//     {
//         name: "Ecuador",
//         flag: "🇪🇨",
//         code: "EC",
//         dial_code: "+593"
//     },
//     {
//         name: "Egypt",
//         flag: "🇪🇬",
//         code: "EG",
//         dial_code: "+20"
//     },
//     {
//         name: "El Salvador",
//         flag: "🇸🇻",
//         code: "SV",
//         dial_code: "+503"
//     },
//     {
//         name: "Equatorial Guinea",
//         flag: "🇬🇶",
//         code: "GQ",
//         dial_code: "+240"
//     },
//     {
//         name: "Eritrea",
//         flag: "🇪🇷",
//         code: "ER",
//         dial_code: "+291"
//     },
//     {
//         name: "Estonia",
//         flag: "🇪🇪",
//         code: "EE",
//         dial_code: "+372"
//     },
//     {
//         name: "Ethiopia",
//         flag: "🇪🇹",
//         code: "ET",
//         dial_code: "+251"
//     },
//     {
//         name: "Falkland Islands (Malvinas)",
//         flag: "🇫🇰",
//         code: "FK",
//         dial_code: "+500"
//     },
//     {
//         name: "Faroe Islands",
//         flag: "🇫🇴",
//         code: "FO",
//         dial_code: "+298"
//     },
//     {
//         name: "Fiji",
//         flag: "🇫🇯",
//         code: "FJ",
//         dial_code: "+679"
//     },
//     {
//         name: "Finland",
//         flag: "🇫🇮",
//         code: "FI",
//         dial_code: "+358"
//     },
//     {
//         name: "France",
//         flag: "🇫🇷",
//         code: "FR",
//         dial_code: "+33"
//     },
//     {
//         name: "French Guiana",
//         flag: "🇬🇫",
//         code: "GF",
//         dial_code: "+594"
//     },
//     {
//         name: "French Polynesia",
//         flag: "🇵🇫",
//         code: "PF",
//         dial_code: "+689"
//     },
//     {
//         name: "French Southern Territories",
//         flag: "🇹🇫",
//         code: "TF",
//         dial_code: "+262"
//     },
//     {
//         name: "Gabon",
//         flag: "🇬🇦",
//         code: "GA",
//         dial_code: "+241"
//     },
//     {
//         name: "Gambia",
//         flag: "🇬🇲",
//         code: "GM",
//         dial_code: "+220"
//     },
//     {
//         name: "Georgia",
//         flag: "🇬🇪",
//         code: "GE",
//         dial_code: "+995"
//     },
//     {
//         name: "Germany",
//         flag: "🇩🇪",
//         code: "DE",
//         dial_code: "+49"
//     },
//     {
//         name: "Ghana",
//         flag: "🇬🇭",
//         code: "GH",
//         dial_code: "+233"
//     },
//     {
//         name: "Gibraltar",
//         flag: "🇬🇮",
//         code: "GI",
//         dial_code: "+350"
//     },
//     {
//         name: "Greece",
//         flag: "🇬🇷",
//         code: "GR",
//         dial_code: "+30"
//     },
//     {
//         name: "Greenland",
//         flag: "🇬🇱",
//         code: "GL",
//         dial_code: "+299"
//     },
//     {
//         name: "Grenada",
//         flag: "🇬🇩",
//         code: "GD",
//         dial_code: "+1473"
//     },
//     {
//         name: "Guadeloupe",
//         flag: "🇬🇵",
//         code: "GP",
//         dial_code: "+590"
//     },
//     {
//         name: "Guam",
//         flag: "🇬🇺",
//         code: "GU",
//         dial_code: "+1671"
//     },
//     {
//         name: "Guatemala",
//         flag: "🇬🇹",
//         code: "GT",
//         dial_code: "+502"
//     },
//     {
//         name: "Guernsey",
//         flag: "🇬🇬",
//         code: "GG",
//         dial_code: "+44"
//     },
//     {
//         name: "Guinea",
//         flag: "🇬🇳",
//         code: "GN",
//         dial_code: "+224"
//     },
//     {
//         name: "Guinea-Bissau",
//         flag: "🇬🇼",
//         code: "GW",
//         dial_code: "+245"
//     },
//     {
//         name: "Guyana",
//         flag: "🇬🇾",
//         code: "GY",
//         dial_code: "+592"
//     },
//     {
//         name: "Haiti",
//         flag: "🇭🇹",
//         code: "HT",
//         dial_code: "+509"
//     },
//     {
//         name: "Heard Island and Mcdonald Islands",
//         flag: "🇭🇲",
//         code: "HM",
//         dial_code: "+672"
//     },
//     {
//         name: "Holy See (Vatican City State)",
//         flag: "🇻🇦",
//         code: "VA",
//         dial_code: "+379"
//     },
//     {
//         name: "Honduras",
//         flag: "🇭🇳",
//         code: "HN",
//         dial_code: "+504"
//     },
//     {
//         name: "Hong Kong",
//         flag: "🇭🇰",
//         code: "HK",
//         dial_code: "+852"
//     },
//     {
//         name: "Hungary",
//         flag: "🇭🇺",
//         code: "HU",
//         dial_code: "+36"
//     },
//     {
//         name: "Iceland",
//         flag: "🇮🇸",
//         code: "IS",
//         dial_code: "+354"
//     },
//     {
//         name: "India",
//         flag: "🇮🇳",
//         code: "IN",
//         dial_code: "+91"
//     },
//     {
//         name: "Indonesia",
//         flag: "🇮🇩",
//         code: "ID",
//         dial_code: "+62"
//     },
//     {
//         name: "Iran, Islamic Republic of Persian Gulf",
//         flag: "🇮🇷",
//         code: "IR",
//         dial_code: "+98"
//     },
//     {
//         name: "Iraq",
//         flag: "🇮🇶",
//         code: "IQ",
//         dial_code: "+964"
//     },
//     {
//         name: "Ireland",
//         flag: "🇮🇪",
//         code: "IE",
//         dial_code: "+353"
//     },
//     {
//         name: "Isle of Man",
//         flag: "🇮🇲",
//         code: "IM",
//         dial_code: "+44"
//     },
//     {
//         name: "Israel",
//         flag: "🇮🇱",
//         code: "IL",
//         dial_code: "+972"
//     },
//     {
//         name: "Italy",
//         flag: "🇮🇹",
//         code: "IT",
//         dial_code: "+39"
//     },
//     {
//         name: "Jamaica",
//         flag: "🇯🇲",
//         code: "JM",
//         dial_code: "+1876"
//     },
//     {
//         name: "Japan",
//         flag: "🇯🇵",
//         code: "JP",
//         dial_code: "+81"
//     },
//     {
//         name: "Jersey",
//         flag: "🇯🇪",
//         code: "JE",
//         dial_code: "+44"
//     },
//     {
//         name: "Jordan",
//         flag: "🇯🇴",
//         code: "JO",
//         dial_code: "+962"
//     },
//     {
//         name: "Kazakhstan",
//         flag: "🇰🇿",
//         code: "KZ",
//         dial_code: "+7"
//     },
//     {
//         name: "Kenya",
//         flag: "🇰🇪",
//         code: "KE",
//         dial_code: "+254"
//     },
//     {
//         name: "Kiribati",
//         flag: "🇰🇮",
//         code: "KI",
//         dial_code: "+686"
//     },
//     {
//         name: "Korea, Democratic People's Republic of Korea",
//         flag: "🇰🇵",
//         code: "KP",
//         dial_code: "+850"
//     },
//     {
//         name: "Korea, Republic of South Korea",
//         flag: "🇰🇷",
//         code: "KR",
//         dial_code: "+82"
//     },
//     {
//         name: "Kosovo",
//         flag: "🇽🇰",
//         code: "XK",
//         dial_code: "+383"
//     },
//     {
//         name: "Kuwait",
//         flag: "🇰🇼",
//         code: "KW",
//         dial_code: "+965"
//     },
//     {
//         name: "Kyrgyzstan",
//         flag: "🇰🇬",
//         code: "KG",
//         dial_code: "+996"
//     },
//     {
//         name: "Laos",
//         flag: "🇱🇦",
//         code: "LA",
//         dial_code: "+856"
//     },
//     {
//         name: "Latvia",
//         flag: "🇱🇻",
//         code: "LV",
//         dial_code: "+371"
//     },
//     {
//         name: "Lebanon",
//         flag: "🇱🇧",
//         code: "LB",
//         dial_code: "+961"
//     },
//     {
//         name: "Lesotho",
//         flag: "🇱🇸",
//         code: "LS",
//         dial_code: "+266"
//     },
//     {
//         name: "Liberia",
//         flag: "🇱🇷",
//         code: "LR",
//         dial_code: "+231"
//     },
//     {
//         name: "Libyan Arab Jamahiriya",
//         flag: "🇱🇾",
//         code: "LY",
//         dial_code: "+218"
//     },
//     {
//         name: "Liechtenstein",
//         flag: "🇱🇮",
//         code: "LI",
//         dial_code: "+423"
//     },
//     {
//         name: "Lithuania",
//         flag: "🇱🇹",
//         code: "LT",
//         dial_code: "+370"
//     },
//     {
//         name: "Luxembourg",
//         flag: "🇱🇺",
//         code: "LU",
//         dial_code: "+352"
//     },
//     {
//         name: "Macao",
//         flag: "🇲🇴",
//         code: "MO",
//         dial_code: "+853"
//     },
//     {
//         name: "Macedonia",
//         flag: "🇲🇰",
//         code: "MK",
//         dial_code: "+389"
//     },
//     {
//         name: "Madagascar",
//         flag: "🇲🇬",
//         code: "MG",
//         dial_code: "+261"
//     },
//     {
//         name: "Malawi",
//         flag: "🇲🇼",
//         code: "MW",
//         dial_code: "+265"
//     },
//     {
//         name: "Malaysia",
//         flag: "🇲🇾",
//         code: "MY",
//         dial_code: "+60"
//     },
//     {
//         name: "Maldives",
//         flag: "🇲🇻",
//         code: "MV",
//         dial_code: "+960"
//     },
//     {
//         name: "Mali",
//         flag: "🇲🇱",
//         code: "ML",
//         dial_code: "+223"
//     },
//     {
//         name: "Malta",
//         flag: "🇲🇹",
//         code: "MT",
//         dial_code: "+356"
//     },
//     {
//         name: "Marshall Islands",
//         flag: "🇲🇭",
//         code: "MH",
//         dial_code: "+692"
//     },
//     {
//         name: "Martinique",
//         flag: "🇲🇶",
//         code: "MQ",
//         dial_code: "+596"
//     },
//     {
//         name: "Mauritania",
//         flag: "🇲🇷",
//         code: "MR",
//         dial_code: "+222"
//     },
//     {
//         name: "Mauritius",
//         flag: "🇲🇺",
//         code: "MU",
//         dial_code: "+230"
//     },
//     {
//         name: "Mayotte",
//         flag: "🇾🇹",
//         code: "YT",
//         dial_code: "+262"
//     },
//     {
//         name: "Mexico",
//         flag: "🇲🇽",
//         code: "MX",
//         dial_code: "+52"
//     },
//     {
//         name: "Micronesia, Federated States of Micronesia",
//         flag: "🇫🇲",
//         code: "FM",
//         dial_code: "+691"
//     },
//     {
//         name: "Moldova",
//         flag: "🇲🇩",
//         code: "MD",
//         dial_code: "+373"
//     },
//     {
//         name: "Monaco",
//         flag: "🇲🇨",
//         code: "MC",
//         dial_code: "+377"
//     },
//     {
//         name: "Mongolia",
//         flag: "🇲🇳",
//         code: "MN",
//         dial_code: "+976"
//     },
//     {
//         name: "Montenegro",
//         flag: "🇲🇪",
//         code: "ME",
//         dial_code: "+382"
//     },
//     {
//         name: "Montserrat",
//         flag: "🇲🇸",
//         code: "MS",
//         dial_code: "+1664"
//     },
//     {
//         name: "Morocco",
//         flag: "🇲🇦",
//         code: "MA",
//         dial_code: "+212"
//     },
//     {
//         name: "Mozambique",
//         flag: "🇲🇿",
//         code: "MZ",
//         dial_code: "+258"
//     },
//     {
//         name: "Myanmar",
//         flag: "🇲🇲",
//         code: "MM",
//         dial_code: "+95"
//     },
//     {
//         name: "Namibia",
//         flag: "🇳🇦",
//         code: "NA",
//         dial_code: "+264"
//     },
//     {
//         name: "Nauru",
//         flag: "🇳🇷",
//         code: "NR",
//         dial_code: "+674"
//     },
//     {
//         name: "Nepal",
//         flag: "🇳🇵",
//         code: "NP",
//         dial_code: "+977"
//     },
//     {
//         name: "Netherlands",
//         flag: "🇳🇱",
//         code: "NL",
//         dial_code: "+31"
//     },
//     {
//         name: "Netherlands Antilles",
//         flag: "",
//         code: "AN",
//         dial_code: "+599"
//     },
//     {
//         name: "New Caledonia",
//         flag: "🇳🇨",
//         code: "NC",
//         dial_code: "+687"
//     },
//     {
//         name: "New Zealand",
//         flag: "🇳🇿",
//         code: "NZ",
//         dial_code: "+64"
//     },
//     {
//         name: "Nicaragua",
//         flag: "🇳🇮",
//         code: "NI",
//         dial_code: "+505"
//     },
//     {
//         name: "Niger",
//         flag: "🇳🇪",
//         code: "NE",
//         dial_code: "+227"
//     },
//     {
//         name: "Nigeria",
//         flag: "🇳🇬",
//         code: "NG",
//         dial_code: "+234"
//     },
//     {
//         name: "Niue",
//         flag: "🇳🇺",
//         code: "NU",
//         dial_code: "+683"
//     },
//     {
//         name: "Norfolk Island",
//         flag: "🇳🇫",
//         code: "NF",
//         dial_code: "+672"
//     },
//     {
//         name: "Northern Mariana Islands",
//         flag: "🇲🇵",
//         code: "MP",
//         dial_code: "+1670"
//     },
//     {
//         name: "Norway",
//         flag: "🇳🇴",
//         code: "NO",
//         dial_code: "+47"
//     },
//     {
//         name: "Oman",
//         flag: "🇴🇲",
//         code: "OM",
//         dial_code: "+968"
//     },
//     {
//         name: "Pakistan",
//         flag: "🇵🇰",
//         code: "PK",
//         dial_code: "+92"
//     },
//     {
//         name: "Palau",
//         flag: "🇵🇼",
//         code: "PW",
//         dial_code: "+680"
//     },
//     {
//         name: "Palestinian Territory, Occupied",
//         flag: "🇵🇸",
//         code: "PS",
//         dial_code: "+970"
//     },
//     {
//         name: "Panama",
//         flag: "🇵🇦",
//         code: "PA",
//         dial_code: "+507"
//     },
//     {
//         name: "Papua New Guinea",
//         flag: "🇵🇬",
//         code: "PG",
//         dial_code: "+675"
//     },
//     {
//         name: "Paraguay",
//         flag: "🇵🇾",
//         code: "PY",
//         dial_code: "+595"
//     },
//     {
//         name: "Peru",
//         flag: "🇵🇪",
//         code: "PE",
//         dial_code: "+51"
//     },
//     {
//         name: "Philippines",
//         flag: "🇵🇭",
//         code: "PH",
//         dial_code: "+63"
//     },
//     {
//         name: "Pitcairn",
//         flag: "🇵🇳",
//         code: "PN",
//         dial_code: "+64"
//     },
//     {
//         name: "Poland",
//         flag: "🇵🇱",
//         code: "PL",
//         dial_code: "+48"
//     },
//     {
//         name: "Portugal",
//         flag: "🇵🇹",
//         code: "PT",
//         dial_code: "+351"
//     },
//     {
//         name: "Puerto Rico",
//         flag: "🇵🇷",
//         code: "PR",
//         dial_code: "+1939"
//     },
//     {
//         name: "Qatar",
//         flag: "🇶🇦",
//         code: "QA",
//         dial_code: "+974"
//     },
//     {
//         name: "Romania",
//         flag: "🇷🇴",
//         code: "RO",
//         dial_code: "+40"
//     },
//     {
//         name: "Russia",
//         flag: "🇷🇺",
//         code: "RU",
//         dial_code: "+7"
//     },
//     {
//         name: "Rwanda",
//         flag: "🇷🇼",
//         code: "RW",
//         dial_code: "+250"
//     },
//     {
//         name: "Reunion",
//         flag: "🇷🇪",
//         code: "RE",
//         dial_code: "+262"
//     },
//     {
//         name: "Saint Barthelemy",
//         flag: "🇧🇱",
//         code: "BL",
//         dial_code: "+590"
//     },
//     {
//         name: "Saint Helena, Ascension and Tristan Da Cunha",
//         flag: "🇸🇭",
//         code: "SH",
//         dial_code: "+290"
//     },
//     {
//         name: "Saint Kitts and Nevis",
//         flag: "🇰🇳",
//         code: "KN",
//         dial_code: "+1869"
//     },
//     {
//         name: "Saint Lucia",
//         flag: "🇱🇨",
//         code: "LC",
//         dial_code: "+1758"
//     },
//     {
//         name: "Saint Martin",
//         flag: "🇲🇫",
//         code: "MF",
//         dial_code: "+590"
//     },
//     {
//         name: "Saint Pierre and Miquelon",
//         flag: "🇵🇲",
//         code: "PM",
//         dial_code: "+508"
//     },
//     {
//         name: "Saint Vincent and the Grenadines",
//         flag: "🇻🇨",
//         code: "VC",
//         dial_code: "+1784"
//     },
//     {
//         name: "Samoa",
//         flag: "🇼🇸",
//         code: "WS",
//         dial_code: "+685"
//     },
//     {
//         name: "San Marino",
//         flag: "🇸🇲",
//         code: "SM",
//         dial_code: "+378"
//     },
//     {
//         name: "Sao Tome and Principe",
//         flag: "🇸🇹",
//         code: "ST",
//         dial_code: "+239"
//     },
//     {
//         name: "Saudi Arabia",
//         flag: "🇸🇦",
//         code: "SA",
//         dial_code: "+966"
//     },
//     {
//         name: "Senegal",
//         flag: "🇸🇳",
//         code: "SN",
//         dial_code: "+221"
//     },
//     {
//         name: "Serbia",
//         flag: "🇷🇸",
//         code: "RS",
//         dial_code: "+381"
//     },
//     {
//         name: "Seychelles",
//         flag: "🇸🇨",
//         code: "SC",
//         dial_code: "+248"
//     },
//     {
//         name: "Sierra Leone",
//         flag: "🇸🇱",
//         code: "SL",
//         dial_code: "+232"
//     },
//     {
//         name: "Singapore",
//         flag: "🇸🇬",
//         code: "SG",
//         dial_code: "+65"
//     },
//     {
//         name: "Slovakia",
//         flag: "🇸🇰",
//         code: "SK",
//         dial_code: "+421"
//     },
//     {
//         name: "Slovenia",
//         flag: "🇸🇮",
//         code: "SI",
//         dial_code: "+386"
//     },
//     {
//         name: "Solomon Islands",
//         flag: "🇸🇧",
//         code: "SB",
//         dial_code: "+677"
//     },
//     {
//         name: "Somalia",
//         flag: "🇸🇴",
//         code: "SO",
//         dial_code: "+252"
//     },
//     {
//         name: "South Africa",
//         flag: "🇿🇦",
//         code: "ZA",
//         dial_code: "+27"
//     },
//     {
//         name: "South Sudan",
//         flag: "🇸🇸",
//         code: "SS",
//         dial_code: "+211"
//     },
//     {
//         name: "South Georgia and the South Sandwich Islands",
//         flag: "🇬🇸",
//         code: "GS",
//         dial_code: "+500"
//     },
//     {
//         name: "Spain",
//         flag: "🇪🇸",
//         code: "ES",
//         dial_code: "+34"
//     },
//     {
//         name: "Sri Lanka",
//         flag: "🇱🇰",
//         code: "LK",
//         dial_code: "+94"
//     },
//     {
//         name: "Sudan",
//         flag: "🇸🇩",
//         code: "SD",
//         dial_code: "+249"
//     },
//     {
//         name: "Suriname",
//         flag: "🇸🇷",
//         code: "SR",
//         dial_code: "+597"
//     },
//     {
//         name: "Svalbard and Jan Mayen",
//         flag: "🇸🇯",
//         code: "SJ",
//         dial_code: "+47"
//     },
//     {
//         name: "Swaziland",
//         flag: "🇸🇿",
//         code: "SZ",
//         dial_code: "+268"
//     },
//     {
//         name: "Sweden",
//         flag: "🇸🇪",
//         code: "SE",
//         dial_code: "+46"
//     },
//     {
//         name: "Switzerland",
//         flag: "🇨🇭",
//         code: "CH",
//         dial_code: "+41"
//     },
//     {
//         name: "Syrian Arab Republic",
//         flag: "🇸🇾",
//         code: "SY",
//         dial_code: "+963"
//     },
//     {
//         name: "Taiwan",
//         flag: "🇹🇼",
//         code: "TW",
//         dial_code: "+886"
//     },
//     {
//         name: "Tajikistan",
//         flag: "🇹🇯",
//         code: "TJ",
//         dial_code: "+992"
//     },
//     {
//         name: "Tanzania, United Republic of Tanzania",
//         flag: "🇹🇿",
//         code: "TZ",
//         dial_code: "+255"
//     },
//     {
//         name: "Thailand",
//         flag: "🇹🇭",
//         code: "TH",
//         dial_code: "+66"
//     },
//     {
//         name: "Timor-Leste",
//         flag: "🇹🇱",
//         code: "TL",
//         dial_code: "+670"
//     },
//     {
//         name: "Togo",
//         flag: "🇹🇬",
//         code: "TG",
//         dial_code: "+228"
//     },
//     {
//         name: "Tokelau",
//         flag: "🇹🇰",
//         code: "TK",
//         dial_code: "+690"
//     },
//     {
//         name: "Tonga",
//         flag: "🇹🇴",
//         code: "TO",
//         dial_code: "+676"
//     },
//     {
//         name: "Trinidad and Tobago",
//         flag: "🇹🇹",
//         code: "TT",
//         dial_code: "+1868"
//     },
//     {
//         name: "Tunisia",
//         flag: "🇹🇳",
//         code: "TN",
//         dial_code: "+216"
//     },
//     {
//         name: "Turkey",
//         flag: "🇹🇷",
//         code: "TR",
//         dial_code: "+90"
//     },
//     {
//         name: "Turkmenistan",
//         flag: "🇹🇲",
//         code: "TM",
//         dial_code: "+993"
//     },
//     {
//         name: "Turks and Caicos Islands",
//         flag: "🇹🇨",
//         code: "TC",
//         dial_code: "+1649"
//     },
//     {
//         name: "Tuvalu",
//         flag: "🇹🇻",
//         code: "TV",
//         dial_code: "+688"
//     },
//     {
//         name: "Uganda",
//         flag: "🇺🇬",
//         code: "UG",
//         dial_code: "+256"
//     },
//     {
//         name: "Ukraine",
//         flag: "🇺🇦",
//         code: "UA",
//         dial_code: "+380"
//     },
//     {
//         name: "United Arab Emirates",
//         flag: "🇦🇪",
//         code: "AE",
//         dial_code: "+971"
//     },
//     {
//         name: "United Kingdom",
//         flag: "🇬🇧",
//         code: "GB",
//         dial_code: "+44"
//     },
//     {
//         name: "United States",
//         flag: "🇺🇸",
//         code: "US",
//         dial_code: "+1"
//     },
//     {
//         name: "Uruguay",
//         flag: "🇺🇾",
//         code: "UY",
//         dial_code: "+598"
//     },
//     {
//         name: "Uzbekistan",
//         flag: "🇺🇿",
//         code: "UZ",
//         dial_code: "+998"
//     },
//     {
//         name: "Vanuatu",
//         flag: "🇻🇺",
//         code: "VU",
//         dial_code: "+678"
//     },
//     {
//         name: "Venezuela, Bolivarian Republic of Venezuela",
//         flag: "🇻🇪",
//         code: "VE",
//         dial_code: "+58"
//     },
//     {
//         name: "Vietnam",
//         flag: "🇻🇳",
//         code: "VN",
//         dial_code: "+84"
//     },
//     {
//         name: "Virgin Islands, British",
//         flag: "🇻🇬",
//         code: "VG",
//         dial_code: "+1284"
//     },
//     {
//         name: "Virgin Islands, U.S.",
//         flag: "🇻🇮",
//         code: "VI",
//         dial_code: "+1340"
//     },
//     {
//         name: "Wallis and Futuna",
//         flag: "🇼🇫",
//         code: "WF",
//         dial_code: "+681"
//     },
//     {
//         name: "Yemen",
//         flag: "🇾🇪",
//         code: "YE",
//         dial_code: "+967"
//     },
//     {
//         name: "Zambia",
//         flag: "🇿🇲",
//         code: "ZM",
//         dial_code: "+260"
//     },
//     {
//         name: "Zimbabwe",
//         flag: "🇿🇼",
//         code: "ZW",
//         dial_code: "+263"
//     }
// ];

// const currencies = ["USD","AUD","CAD","GBP"]
const currencies = [];

var card_type = {
    name: "Steam",
    image: "Steam.png",
    currencies: [
        {
            id: 1,
            name: "USD",
            countrycode: "US",
            flag: null,
            payment_mediums: [
                {
                    id: 1,
                    name: "Physical Card",
                    image: null,
                    pricing: [
                        { value: "$1", rate: "1000" },
                        { value: "$2", rate: "2000" },
                        { value: "$3", rate: "3000" }
                    ]
                },
                {
                    id: 2,
                    name: "E-code",
                    image: null,
                    pricing: [
                        { value: "1", rate: "1000" },
                        { value: "2", rate: "2000" },
                        { value: "3", rate: "3000" }
                    ]
                }
            ],
            buy_sell: 2
        },
        {
            id: 5,
            name: "AUD",
            flag: null,
            payment_mediums: [
                {
                    id: 1,
                    name: "Physical Card",
                    image: null,
                    pricing: [
                        { value: "1", rate: "1000" },
                        { value: "2", rate: "2000" },
                        { value: "3", rate: "3000" }
                    ]
                }
            ],
            buy_sell: 2
        }
    ]
};

// List the available currencies fro the data received
for (let i = 0; i < card_type.currencies.length; i++) {
    currencies.push(card_type.currencies[i].name.toLowerCase().trim());
}

// List out the supported countries currencies
for (let i = 0; i < currencies.length; i++) {
    // let option = `<option alt=${countries_iso[i].code} value=${
    //     countries_iso[i].name
    // }>${countries_iso[i].flag} ${countries_iso[i].name.trim()}</option>`;
    let currency = `<option alt=${currencies[i].toLowerCase()}>${
        currencies[i]
    }</option>`;
    $("#countries_list").append(currency);
}

//Select country to display card types
let currency_code_value = "";
$("#countries_list").on("change", function() {
  $("#cardtype_list").empty()
  $("#cardtype_list").append("<option selected>Select currency</option>")
    const e = document.getElementById("countries_list");
    const currency_code = e.options[e.selectedIndex]
        .getAttribute("alt")
        .toLowerCase()
        .trim();

    currency_code_value = currency_code;

    // Populate card types after currency is chosen
    card_type.currencies.map(currency => {
        if (currency.name.toLowerCase().trim() == currency_code) {
            currency.payment_mediums.map(payment_medium => {
                let cardtypenamecontent = `<option alt="${payment_medium.name.toLowerCase()}" value="${payment_medium.name.toLowerCase()}">${
                    payment_medium.name
                }</option>`;
                $("#cardtype_list").append(cardtypenamecontent);
            });
            $("#card_type").addClass("d-block");
            $(".card-price-qty").addClass("d-flex");
        }
    });
});

//List out rates depending on the chosen card type
$("#cardtype_list").on("change", function() {
  $("#cardprice").empty()
  $("#cardprice").append("<option selected>Select Price</option>")
    const e = document.getElementById("cardtype_list");
    const cardtypetext = e.options[e.selectedIndex]
        .getAttribute("alt")
        .toLowerCase()
        .trim();

    card_type.currencies.map(currency => {
        if (currency.name.toLowerCase().trim() == currency_code_value.trim()) {
            currency.payment_mediums.map(pmedium => {
                if (pmedium.name.toLowerCase().trim() == cardtypetext) {
                    pmedium.pricing.map(pricing => {
                        let cardpricelist = `<option alt="${pricing.rate.trim()}" value="${pricing.value.trim()}">${pricing.value.trim()}</option>`;
                        $("#cardprice").append(cardpricelist);
                    });
                }
            });
        }
    });
});

//Select price of card for transaction
$("#cardprice").on("change", function() {
    const e = document.getElementById("cardprice");
    const rate = e.options[e.selectedIndex]
        .getAttribute("alt")
        .toLowerCase()
        .trim();

    $("#price_per_card").text("₦" + rate);
});

let basket = [];
$("#addcard_button").on("click", function() {
  if($("#price_per_card").text().trim().length == 0) {
    alert("Choose a price")
    return
  }

  const e = document.getElementById("cardtype_list");
  const cardtypetext = e.options[e.selectedIndex]
      .getAttribute("alt")
      .toLowerCase()
      .trim();

    let cardprice = $("#cardprice")
        .val()
        .trim();
    let price_per_card = $("#price_per_card")
        .text()
        .trim();
    let quantity = $("#qty_input")
        .val()
        .trim();
    let total = price_per_card.slice(1) * quantity;

    const template = `<tr class="my-2">
                        <td>${cardprice}</td>
                        <td>${price_per_card}</td>
                        <td>x${quantity}</td>
                        <td id="totalprice">${total}</td>
                        <td id="removeitem" class="removeitem"><span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.47 2 2 6.47 2 12C2 17.53 6.47 22 12 22C17.53 22 22 17.53 22 12C22 6.47 17.53 2 12 2ZM17 15.59L15.59 17L12 13.41L8.41 17L7 15.59L10.59 12L7 8.41L8.41 7L12 10.59L15.59 7L17 8.41L13.41 12L17 15.59Z" fill="#CD0B0B"/>
                        </svg>
                        </span></td>
                    </tr>`;

    basket.push(total);

    $("#selectedcardslist").append(template);
    $("#total_price").addClass("d-flex");
    $("#nocardavailable").hide();
    $("#totalAmount").html("N " + basket.reduce(addtotals));
    $("#list_added_cards").removeClass("d-none");
});

function addtotals(total, num) {
    return total + num;
}

//Preview image before upload
$("#proceedtoupload").on("click", function() {
    // $("#uploadcardimage").trigger("click");
    $("#uploadCardImageModal").show();
});

function preview(input) {
    $("#upload_text_desc").hide();
    if (input.files && input.files[0]) {
        $(input.files).each(function() {
            var reader = new FileReader();
            reader.readAsDataURL(this);
            reader.onload = function(e) {
                $("#previewImg").append(
                    "<img class='thumb m-2 zoom' src='" + e.target.result + "'>"
                );
            };
        });
    }
}

function inputfile() {
    $("#previewImg").empty();
    $("#uploadcardsform")
        .get(0)
        .reset();
    $("#uploadCardImageModal").hide();
}

$("table").on("click", "td.removeitem", function() {
    const pricetoremove = $(this)
        .siblings("#totalprice")
        .text()
        .trim();

    const matchingitem = basket.findIndex(price => price == pricetoremove);
    basket.splice(matchingitem, 1);

    $(this)
        .parent()
        .remove();

    if (basket.length == 0) {
        $("#totalAmount").html("N " + 0);
        $("#total_price").removeClass("d-flex");
        $("#nocardavailable").show();
    } else {
        $("#totalAmount").html("N " + basket.reduce(addtotals));
    }
});

function changeqty(param) {
    let getval = $("#qty_input").val();
    if (param == "add") {
        $("#qty_input").val(parseInt(getval) + 1);
    } else if (param == "subtract") {
        if (parseInt(getval) == 1) return;
        $("#qty_input").val(parseInt(getval) - 1);
    }
}

// ===== BITCOIN CALCULATOR ======
$("#myTab .nav-item").on("click", function() {
    $("#myTab .nav-item").removeClass("active-title-item");
    $("#myTab .nav-item .nav-link").removeClass("text-white");
    $(this).addClass("active-title-item");
    $("#myTab .active-title-item .nav-link").addClass("text-white");
    $("input").val("")
});

$("#copyWalletAddress").on("click", function() {
    const inputText = document.querySelector("#wallet_address");
    inputText.select();
    inputText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    $("#copied_text").css("display", "block");
    $("#inputText").css("box-shadow", "none !important");
    $("#inputText").css("outline", "none !important");
    setTimeout(function() {
        $("#copied_text").css("display", "none");
    }, 1700);
});

// === Bitcoin calculator onchange logic ===
const sell_usd_per_btc = 10500;
const sell_ngn_per_usd = 400;

const sellusdfield = $("#sell_usd_field");
const sellbtcfield = $("#sell_btc_field");
const sellngnfield = $("#sell_ngn_field");

sellusdfield.on("keyup", function() {
    let value = $(this).val();

    //btc equivalent
    let btc = value / sell_usd_per_btc;
    sellbtcfield.val(btc);

    //naira equivalent
    let naira = value * sell_ngn_per_usd;
    sellngnfield.val(naira);
});

sellbtcfield.on("keyup", function(e) {
    let value = $(this).val();

    //dollar equivalent
    let dollars = value * sell_usd_per_btc;
    sellusdfield.val(dollars);

    //naira equivalent
    let naira = dollars * sell_ngn_per_usd;
    sellngnfield.val(naira);
});

/*===== BUY BITCOIN =====*/
const buy_usd_per_btc = 10465.6;
const buy_ngn_per_usd = 381.5;

const btcfield = $("#buy_btc_field");
const usdfield = $("#buy_usd_field");
const ngnfield = $("#buy_ngn_field");

btcfield.on("keyup", function(e) {
    let value = $(this).val();

    //dollar equivalent
    let dollars = value * buy_usd_per_btc;
    usdfield.val(dollars);

    //naira equivalent
    let naira = dollars * buy_ngn_per_usd;
    ngnfield.val(naira);
});

usdfield.on("keyup", function(e) {
    let value = $(this).val();

    //btc equivalent
    let btc = value / buy_usd_per_btc;
    btcfield.val(btc);

    //naira equivalent
    let naira = value * buy_ngn_per_usd;
    ngnfield.val(naira);
});

ngnfield.on("keyup", function(e) {
    let value = $(this).val();

    //dollar equivalent
    let dollar = value / buy_ngn_per_usd;
    usdfield.val(dollar);

    //naira equivalent
    let btc = dollar / buy_usd_per_btc;
    btcfield.val(btc);
});
/*===== END BUY CRYPTO =====*/

$("#sell_submit_btn").on("click", function(e) {
    e.preventDefault();
    const getAlt = $(this).attr("alt");
    const uploadText = `
    <span class="d-block primary-color text-center" style="font-size:14px;">Please place your Image (proof of payment) here</span>
    <span class="d-block text-center" style="font-size:13px;color: rgba(0, 0, 112, 0.7);letter-spacing: 0.01em;">If you do not have a proof of payment click trade to continue</span>
    `;
    if (getAlt == "sell") {
        $("#upload_text_desc").empty();
        $("#upload_text_desc").append(uploadText);
        $("#upload_card_btn").text("Trade");
        // $("#upload_text_desc")

        $("#uploadCardImageModal").show();
    }
});

$("#upload_pop_success").on("click", function() {
    $("#uploadPopModal").css("display", "none");
});
/*===== END BUY BITCOIN =====*/


const sell_usd_per_eth = 970;
const sell_ngn_per_usd_eth = 381.5;

//Convert USD to ETH, naira equivalent
//Sell ETH
$("#sell_usd_field_eth").on("keyup", function(){
    let value = $(this).val();

    //eth equivalent
    let eth = value / sell_usd_per_eth;
    $("#sell_eth_equiv_field").val(eth);

    // //naira equivalent
    let naira = value * sell_ngn_per_usd_eth;
    $("#sell_ngn_eth_field").val(naira);
})


$("#sell_eth_equiv_field").on("keyup", function(e) {
    let value = $(this).val();

    //dollar equivalent
    let dollars = value * sell_usd_per_eth;
    $("#sell_usd_field_eth").val(dollars);

    //naira equivalent
    let naira = dollars * sell_ngn_per_usd_eth;
    $("#sell_ngn_eth_field").val(naira);
});

//Buy ETH
const buy_usd_per_eth = 5465;
const buy_ngn_per_usd_eth = 280;

$("#buy_usd_field_eth").on("keyup", function() {
    let value = $(this).val();

    //btc equivalent
    let eth = value / buy_usd_per_eth;
    $("#buy_eth_field").val(eth);

    //naira equivalent
    let naira = value * buy_ngn_per_usd_eth;
    $("#buy_ngn_field_eth").val(naira);
});

$("#buy_eth_field").on("keyup", function() {
    let value = $(this).val();

    //dollar equivalent
    let dollars = value * buy_usd_per_eth;
    $("#buy_usd_field_eth").val(dollars);

    //naira equivalent
    let naira = dollars * buy_ngn_per_usd_eth;
    $("#buy_ngn_field_eth").val(naira);
});

//Disable submit button if fields are empty in sell btc tab
$("#wallet_address").on("keyup", function(){
    if($(this).val().trim().length > 10 && $("#sell_ngn_field").val().trim().length > 1) {
        $("#sell_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_submit_btn").attr('disabled',"true")
    }
})
$("#sell_btc_field").on("keyup", function(){
    if($("#wallet_address").val().trim().length > 10 && $("#sell_ngn_field").val().trim().length > 1) {
        $("#sell_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_submit_btn").attr('disabled',"true")
    }
})
$("#sell_usd_field").on("keyup", function(){
    if($("#wallet_address").val().trim().length > 10 && $("#sell_ngn_field").val().trim().length > 1) {
        $("#sell_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_submit_btn").attr('disabled',"true")
    }
})

//Disable submit button if fields are empty in buy btc tab
$("#buy_wallet_address").on("keyup", function(){
    if($(this).val().trim().length > 10 && $("#buy_ngn_field").val().trim().length > 1) {
        $("#buy_btc_btn").removeAttr('disabled')
    } else {
        $("#buy_btc_btn").attr('disabled',"true")
    }
})
$("#buy_btc_field").on("keyup", function(){
    if($("#wallet_address").val().trim().length > 10 && $("#buy_ngn_field").val().trim().length > 1) {
        $("#buy_btc_btn").removeAttr('disabled')
    } else {
        $("#buy_btc_btn").attr('disabled',"true")
    }
})
$("#buy_usd_field").on("keyup", function(){
    if($("#wallet_address").val().trim().length > 10 && $("#buy_ngn_field").val().trim().length > 1) {
        $("#buy_btc_btn").removeAttr('disabled')
    } else {
        $("#buy_btc_btn").attr('disabled',"true")
    }
})


//Disable submit button if fields are empty in sell eth tab
$("#eth_wallet_address").on("keyup", function(){
    if($(this).val().trim().length > 10 && $("#sell_ngn_eth_field").val().trim().length > 1) {
        $("#sell_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_eth_submit_btn").attr('disabled',"true")
    }
})
$("#sell_usd_field_eth").on("keyup", function(){
    if($("#eth_wallet_address").val().trim().length > 10 && $("#sell_ngn_eth_field").val().trim().length > 1) {
        $("#sell_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_eth_submit_btn").attr('disabled',"true")
    }
})
$("#sell_eth_equiv_field").on("keyup", function(){
    if($("#eth_wallet_address").val().trim().length > 10 && $("#sell_ngn_eth_field").val().trim().length > 1) {
        $("#sell_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_eth_submit_btn").attr('disabled',"true")
    }
})

//Disable submit button if fields are empty in buy eth tab
$("#buy_wallet_address").on("keyup", function(){
    if($(this).val().trim().length > 10 && $("#buy_ngn_field_eth").val().trim().length > 1) {
        $("#buy_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#buy_eth_submit_btn").attr('disabled',"true")
    }
})
$("#buy_usd_field_eth").on("keyup", function(){
    if($("#buy_wallet_address").val().trim().length > 10 && $("#buy_ngn_field_eth").val().trim().length > 1) {
        $("#buy_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#buy_eth_submit_btn").attr('disabled',"true")
    }
})
$("#buy_eth_field").on("keyup", function(){
    if($("#buy_wallet_address").val().trim().length > 10 && $("#buy_ngn_field_eth").val().trim().length > 1) {
        $("#buy_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#buy_eth_submit_btn").attr('disabled',"true")
    }
})