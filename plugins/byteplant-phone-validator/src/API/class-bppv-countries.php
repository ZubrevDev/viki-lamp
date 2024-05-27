<?php
/**
 * Translate ISO 3166-1 Country Codes to Country names and vice versa.
 *
 * @package BBPV/API
 */

/**
 * Class BPPV_Countries
 */
class BPPV_Countries {

	/**
	 * The country list.
	 *
	 * @var array
	 */
	private static $countries = array();

	/**
	 * Populate the countries
	 */
	public static function populate() {

		self::$countries = array(
			'AF' => __( 'Afghanistan', 'byteplant-phone-validator' ),
			'AL' => __( 'Albania', 'byteplant-phone-validator' ),
			'DZ' => __( 'Algeria', 'byteplant-phone-validator' ),
			'AS' => __( 'American Samoa', 'byteplant-phone-validator' ),
			'AD' => __( 'Andorra', 'byteplant-phone-validator' ),
			'AO' => __( 'Angola', 'byteplant-phone-validator' ),
			'AI' => __( 'Anguilla', 'byteplant-phone-validator' ),
			'AQ' => __( 'Antarctica', 'byteplant-phone-validator' ),
			'AG' => __( 'Antigua & Barbuda', 'byteplant-phone-validator' ),
			'AR' => __( 'Argentina', 'byteplant-phone-validator' ),
			'AM' => __( 'Armenia', 'byteplant-phone-validator' ),
			'AW' => __( 'Aruba', 'byteplant-phone-validator' ),
			'AU' => __( 'Australia', 'byteplant-phone-validator' ),
			'AT' => __( 'Austria', 'byteplant-phone-validator' ),
			'AZ' => __( 'Azerbaijan', 'byteplant-phone-validator' ),
			'BS' => __( 'Bahamas', 'byteplant-phone-validator' ),
			'BH' => __( 'Bahrain', 'byteplant-phone-validator' ),
			'BD' => __( 'Bangladesh', 'byteplant-phone-validator' ),
			'BB' => __( 'Barbados', 'byteplant-phone-validator' ),
			'BY' => __( 'Belarus', 'byteplant-phone-validator' ),
			'BE' => __( 'Belgium', 'byteplant-phone-validator' ),
			'BZ' => __( 'Belize', 'byteplant-phone-validator' ),
			'BJ' => __( 'Benin', 'byteplant-phone-validator' ),
			'BM' => __( 'Bermuda', 'byteplant-phone-validator' ),
			'BT' => __( 'Bhutan', 'byteplant-phone-validator' ),
			'BO' => __( 'Bolivia', 'byteplant-phone-validator' ),
			'BA' => __( 'Bosnia', 'byteplant-phone-validator' ),
			'BW' => __( 'Botswana', 'byteplant-phone-validator' ),
			'BV' => __( 'Bouvet Island', 'byteplant-phone-validator' ),
			'BR' => __( 'Brazil', 'byteplant-phone-validator' ),
			'IO' => __( 'British Indian Ocean Territory', 'byteplant-phone-validator' ),
			'VG' => __( 'British Virgin Islands', 'byteplant-phone-validator' ),
			'BN' => __( 'Brunei', 'byteplant-phone-validator' ),
			'BG' => __( 'Bulgaria', 'byteplant-phone-validator' ),
			'BF' => __( 'Burkina Faso', 'byteplant-phone-validator' ),
			'BI' => __( 'Burundi', 'byteplant-phone-validator' ),
			'KH' => __( 'Cambodia', 'byteplant-phone-validator' ),
			'CM' => __( 'Cameroon', 'byteplant-phone-validator' ),
			'CA' => __( 'Canada', 'byteplant-phone-validator' ),
			'CV' => __( 'Cape Verde', 'byteplant-phone-validator' ),
			'BQ' => __( 'Caribbean Netherlands', 'byteplant-phone-validator' ),
			'KY' => __( 'Cayman Islands', 'byteplant-phone-validator' ),
			'CF' => __( 'Central African Republic', 'byteplant-phone-validator' ),
			'TD' => __( 'Chad', 'byteplant-phone-validator' ),
			'CL' => __( 'Chile', 'byteplant-phone-validator' ),
			'CN' => __( 'China', 'byteplant-phone-validator' ),
			'CX' => __( 'Christmas Island', 'byteplant-phone-validator' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'byteplant-phone-validator' ),
			'CO' => __( 'Colombia', 'byteplant-phone-validator' ),
			'KM' => __( 'Comoros', 'byteplant-phone-validator' ),
			'CG' => __( 'Congo - Brazzaville', 'byteplant-phone-validator' ),
			'CD' => __( 'Congo - Kinshasa', 'byteplant-phone-validator' ),
			'CK' => __( 'Cook Islands', 'byteplant-phone-validator' ),
			'CR' => __( 'Costa Rica', 'byteplant-phone-validator' ),
			'HR' => __( 'Croatia', 'byteplant-phone-validator' ),
			'CU' => __( 'Cuba', 'byteplant-phone-validator' ),
			'CW' => __( 'Curaçao', 'byteplant-phone-validator' ),
			'CY' => __( 'Cyprus', 'byteplant-phone-validator' ),
			'CZ' => __( 'Czechia', 'byteplant-phone-validator' ),
			'CI' => __( 'Côte d’Ivoire', 'byteplant-phone-validator' ),
			'DK' => __( 'Denmark', 'byteplant-phone-validator' ),
			'DJ' => __( 'Djibouti', 'byteplant-phone-validator' ),
			'DM' => __( 'Dominica', 'byteplant-phone-validator' ),
			'DO' => __( 'Dominican Republic', 'byteplant-phone-validator' ),
			'EC' => __( 'Ecuador', 'byteplant-phone-validator' ),
			'EG' => __( 'Egypt', 'byteplant-phone-validator' ),
			'SV' => __( 'El Salvador', 'byteplant-phone-validator' ),
			'GQ' => __( 'Equatorial Guinea', 'byteplant-phone-validator' ),
			'ER' => __( 'Eritrea', 'byteplant-phone-validator' ),
			'EE' => __( 'Estonia', 'byteplant-phone-validator' ),
			'ET' => __( 'Ethiopia', 'byteplant-phone-validator' ),
			'FK' => __( 'Falkland Islands', 'byteplant-phone-validator' ),
			'FO' => __( 'Faroe Islands', 'byteplant-phone-validator' ),
			'FJ' => __( 'Fiji', 'byteplant-phone-validator' ),
			'FI' => __( 'Finland', 'byteplant-phone-validator' ),
			'FR' => __( 'France', 'byteplant-phone-validator' ),
			'GF' => __( 'French Guiana', 'byteplant-phone-validator' ),
			'PF' => __( 'French Polynesia', 'byteplant-phone-validator' ),
			'TF' => __( 'French Southern Territories', 'byteplant-phone-validator' ),
			'GA' => __( 'Gabon', 'byteplant-phone-validator' ),
			'GM' => __( 'Gambia', 'byteplant-phone-validator' ),
			'GE' => __( 'Georgia', 'byteplant-phone-validator' ),
			'DE' => __( 'Germany', 'byteplant-phone-validator' ),
			'GH' => __( 'Ghana', 'byteplant-phone-validator' ),
			'GI' => __( 'Gibraltar', 'byteplant-phone-validator' ),
			'GR' => __( 'Greece', 'byteplant-phone-validator' ),
			'GL' => __( 'Greenland', 'byteplant-phone-validator' ),
			'GD' => __( 'Grenada', 'byteplant-phone-validator' ),
			'GP' => __( 'Guadeloupe', 'byteplant-phone-validator' ),
			'GU' => __( 'Guam', 'byteplant-phone-validator' ),
			'GT' => __( 'Guatemala', 'byteplant-phone-validator' ),
			'GG' => __( 'Guernsey', 'byteplant-phone-validator' ),
			'GN' => __( 'Guinea', 'byteplant-phone-validator' ),
			'GW' => __( 'Guinea-Bissau', 'byteplant-phone-validator' ),
			'GY' => __( 'Guyana', 'byteplant-phone-validator' ),
			'HT' => __( 'Haiti', 'byteplant-phone-validator' ),
			'HM' => __( 'Heard & McDonald Islands', 'byteplant-phone-validator' ),
			'HN' => __( 'Honduras', 'byteplant-phone-validator' ),
			'HK' => __( 'Hong Kong', 'byteplant-phone-validator' ),
			'HU' => __( 'Hungary', 'byteplant-phone-validator' ),
			'IS' => __( 'Iceland', 'byteplant-phone-validator' ),
			'IN' => __( 'India', 'byteplant-phone-validator' ),
			'ID' => __( 'Indonesia', 'byteplant-phone-validator' ),
			'IR' => __( 'Iran', 'byteplant-phone-validator' ),
			'IQ' => __( 'Iraq', 'byteplant-phone-validator' ),
			'IE' => __( 'Ireland', 'byteplant-phone-validator' ),
			'IM' => __( 'Isle of Man', 'byteplant-phone-validator' ),
			'IL' => __( 'Israel', 'byteplant-phone-validator' ),
			'IT' => __( 'Italy', 'byteplant-phone-validator' ),
			'JM' => __( 'Jamaica', 'byteplant-phone-validator' ),
			'JP' => __( 'Japan', 'byteplant-phone-validator' ),
			'JE' => __( 'Jersey', 'byteplant-phone-validator' ),
			'JO' => __( 'Jordan', 'byteplant-phone-validator' ),
			'KZ' => __( 'Kazakhstan', 'byteplant-phone-validator' ),
			'KE' => __( 'Kenya', 'byteplant-phone-validator' ),
			'KI' => __( 'Kiribati', 'byteplant-phone-validator' ),
			'KW' => __( 'Kuwait', 'byteplant-phone-validator' ),
			'KG' => __( 'Kyrgyzstan', 'byteplant-phone-validator' ),
			'LA' => __( 'Laos', 'byteplant-phone-validator' ),
			'LV' => __( 'Latvia', 'byteplant-phone-validator' ),
			'LB' => __( 'Lebanon', 'byteplant-phone-validator' ),
			'LS' => __( 'Lesotho', 'byteplant-phone-validator' ),
			'LR' => __( 'Liberia', 'byteplant-phone-validator' ),
			'LY' => __( 'Libya', 'byteplant-phone-validator' ),
			'LI' => __( 'Liechtenstein', 'byteplant-phone-validator' ),
			'LT' => __( 'Lithuania', 'byteplant-phone-validator' ),
			'LU' => __( 'Luxembourg', 'byteplant-phone-validator' ),
			'MO' => __( 'Macau', 'byteplant-phone-validator' ),
			'MK' => __( 'Macedonia', 'byteplant-phone-validator' ),
			'MG' => __( 'Madagascar', 'byteplant-phone-validator' ),
			'MW' => __( 'Malawi', 'byteplant-phone-validator' ),
			'MY' => __( 'Malaysia', 'byteplant-phone-validator' ),
			'MV' => __( 'Maldives', 'byteplant-phone-validator' ),
			'ML' => __( 'Mali', 'byteplant-phone-validator' ),
			'MT' => __( 'Malta', 'byteplant-phone-validator' ),
			'MH' => __( 'Marshall Islands', 'byteplant-phone-validator' ),
			'MQ' => __( 'Martinique', 'byteplant-phone-validator' ),
			'MR' => __( 'Mauritania', 'byteplant-phone-validator' ),
			'MU' => __( 'Mauritius', 'byteplant-phone-validator' ),
			'YT' => __( 'Mayotte', 'byteplant-phone-validator' ),
			'MX' => __( 'Mexico', 'byteplant-phone-validator' ),
			'FM' => __( 'Micronesia', 'byteplant-phone-validator' ),
			'MD' => __( 'Moldova', 'byteplant-phone-validator' ),
			'MC' => __( 'Monaco', 'byteplant-phone-validator' ),
			'MN' => __( 'Mongolia', 'byteplant-phone-validator' ),
			'ME' => __( 'Montenegro', 'byteplant-phone-validator' ),
			'MS' => __( 'Montserrat', 'byteplant-phone-validator' ),
			'MA' => __( 'Morocco', 'byteplant-phone-validator' ),
			'MZ' => __( 'Mozambique', 'byteplant-phone-validator' ),
			'MM' => __( 'Myanmar', 'byteplant-phone-validator' ),
			'NA' => __( 'Namibia', 'byteplant-phone-validator' ),
			'NR' => __( 'Nauru', 'byteplant-phone-validator' ),
			'NP' => __( 'Nepal', 'byteplant-phone-validator' ),
			'NL' => __( 'Netherlands', 'byteplant-phone-validator' ),
			'NC' => __( 'New Caledonia', 'byteplant-phone-validator' ),
			'NZ' => __( 'New Zealand', 'byteplant-phone-validator' ),
			'NI' => __( 'Nicaragua', 'byteplant-phone-validator' ),
			'NE' => __( 'Niger', 'byteplant-phone-validator' ),
			'NG' => __( 'Nigeria', 'byteplant-phone-validator' ),
			'NU' => __( 'Niue', 'byteplant-phone-validator' ),
			'NF' => __( 'Norfolk Island', 'byteplant-phone-validator' ),
			'KP' => __( 'North Korea', 'byteplant-phone-validator' ),
			'MP' => __( 'Northern Mariana Islands', 'byteplant-phone-validator' ),
			'NO' => __( 'Norway', 'byteplant-phone-validator' ),
			'OM' => __( 'Oman', 'byteplant-phone-validator' ),
			'PK' => __( 'Pakistan', 'byteplant-phone-validator' ),
			'PW' => __( 'Palau', 'byteplant-phone-validator' ),
			'PS' => __( 'Palestine', 'byteplant-phone-validator' ),
			'PA' => __( 'Panama', 'byteplant-phone-validator' ),
			'PG' => __( 'Papua New Guinea', 'byteplant-phone-validator' ),
			'PY' => __( 'Paraguay', 'byteplant-phone-validator' ),
			'PE' => __( 'Peru', 'byteplant-phone-validator' ),
			'PH' => __( 'Philippines', 'byteplant-phone-validator' ),
			'PN' => __( 'Pitcairn Islands', 'byteplant-phone-validator' ),
			'PL' => __( 'Poland', 'byteplant-phone-validator' ),
			'PT' => __( 'Portugal', 'byteplant-phone-validator' ),
			'PR' => __( 'Puerto Rico', 'byteplant-phone-validator' ),
			'QA' => __( 'Qatar', 'byteplant-phone-validator' ),
			'RO' => __( 'Romania', 'byteplant-phone-validator' ),
			'RU' => __( 'Russia', 'byteplant-phone-validator' ),
			'RW' => __( 'Rwanda', 'byteplant-phone-validator' ),
			'RE' => __( 'Réunion', 'byteplant-phone-validator' ),
			'WS' => __( 'Samoa', 'byteplant-phone-validator' ),
			'SM' => __( 'San Marino', 'byteplant-phone-validator' ),
			'SA' => __( 'Saudi Arabia', 'byteplant-phone-validator' ),
			'SN' => __( 'Senegal', 'byteplant-phone-validator' ),
			'RS' => __( 'Serbia', 'byteplant-phone-validator' ),
			'SC' => __( 'Seychelles', 'byteplant-phone-validator' ),
			'SL' => __( 'Sierra Leone', 'byteplant-phone-validator' ),
			'SG' => __( 'Singapore', 'byteplant-phone-validator' ),
			'SX' => __( 'Sint Maarten', 'byteplant-phone-validator' ),
			'SK' => __( 'Slovakia', 'byteplant-phone-validator' ),
			'SI' => __( 'Slovenia', 'byteplant-phone-validator' ),
			'SB' => __( 'Solomon Islands', 'byteplant-phone-validator' ),
			'SO' => __( 'Somalia', 'byteplant-phone-validator' ),
			'ZA' => __( 'South Africa', 'byteplant-phone-validator' ),
			'GS' => __( 'South Georgia & South Sandwich Islands', 'byteplant-phone-validator' ),
			'KR' => __( 'South Korea', 'byteplant-phone-validator' ),
			'SS' => __( 'South Sudan', 'byteplant-phone-validator' ),
			'ES' => __( 'Spain', 'byteplant-phone-validator' ),
			'LK' => __( 'Sri Lanka', 'byteplant-phone-validator' ),
			'BL' => __( 'St. Barthélemy', 'byteplant-phone-validator' ),
			'SH' => __( 'St. Helena', 'byteplant-phone-validator' ),
			'KN' => __( 'St. Kitts & Nevis', 'byteplant-phone-validator' ),
			'LC' => __( 'St. Lucia', 'byteplant-phone-validator' ),
			'MF' => __( 'St. Martin', 'byteplant-phone-validator' ),
			'PM' => __( 'St. Pierre & Miquelon', 'byteplant-phone-validator' ),
			'VC' => __( 'St. Vincent & Grenadines', 'byteplant-phone-validator' ),
			'SD' => __( 'Sudan', 'byteplant-phone-validator' ),
			'SR' => __( 'Suriname', 'byteplant-phone-validator' ),
			'SJ' => __( 'Svalbard & Jan Mayen', 'byteplant-phone-validator' ),
			'SZ' => __( 'Swaziland', 'byteplant-phone-validator' ),
			'SE' => __( 'Sweden', 'byteplant-phone-validator' ),
			'CH' => __( 'Switzerland', 'byteplant-phone-validator' ),
			'SY' => __( 'Syria', 'byteplant-phone-validator' ),
			'ST' => __( 'São Tomé & Príncipe', 'byteplant-phone-validator' ),
			'TW' => __( 'Taiwan', 'byteplant-phone-validator' ),
			'TJ' => __( 'Tajikistan', 'byteplant-phone-validator' ),
			'TZ' => __( 'Tanzania', 'byteplant-phone-validator' ),
			'TH' => __( 'Thailand', 'byteplant-phone-validator' ),
			'TL' => __( 'Timor-Leste', 'byteplant-phone-validator' ),
			'TG' => __( 'Togo', 'byteplant-phone-validator' ),
			'TK' => __( 'Tokelau', 'byteplant-phone-validator' ),
			'TO' => __( 'Tonga', 'byteplant-phone-validator' ),
			'TT' => __( 'Trinidad & Tobago', 'byteplant-phone-validator' ),
			'TN' => __( 'Tunisia', 'byteplant-phone-validator' ),
			'TR' => __( 'Turkey', 'byteplant-phone-validator' ),
			'TM' => __( 'Turkmenistan', 'byteplant-phone-validator' ),
			'TC' => __( 'Turks & Caicos Islands', 'byteplant-phone-validator' ),
			'TV' => __( 'Tuvalu', 'byteplant-phone-validator' ),
			'UM' => __( 'U.S. Outlying Islands', 'byteplant-phone-validator' ),
			'VI' => __( 'U.S. Virgin Islands', 'byteplant-phone-validator' ),
			'GB' => __( 'UK', 'byteplant-phone-validator' ),
			'US' => __( 'US', 'byteplant-phone-validator' ),
			'UG' => __( 'Uganda', 'byteplant-phone-validator' ),
			'UA' => __( 'Ukraine', 'byteplant-phone-validator' ),
			'AE' => __( 'United Arab Emirates', 'byteplant-phone-validator' ),
			'UY' => __( 'Uruguay', 'byteplant-phone-validator' ),
			'UZ' => __( 'Uzbekistan', 'byteplant-phone-validator' ),
			'VU' => __( 'Vanuatu', 'byteplant-phone-validator' ),
			'VA' => __( 'Vatican City', 'byteplant-phone-validator' ),
			'VE' => __( 'Venezuela', 'byteplant-phone-validator' ),
			'VN' => __( 'Vietnam', 'byteplant-phone-validator' ),
			'WF' => __( 'Wallis & Futuna', 'byteplant-phone-validator' ),
			'EH' => __( 'Western Sahara', 'byteplant-phone-validator' ),
			'YE' => __( 'Yemen', 'byteplant-phone-validator' ),
			'ZM' => __( 'Zambia', 'byteplant-phone-validator' ),
			'ZW' => __( 'Zimbabwe', 'byteplant-phone-validator' ),
			'AX' => __( 'Åland Islands', 'byteplant-phone-validator' ),
		);
	}

	/**
	 * Get list
	 *
	 * @return array
	 */
	public static function get_list() {
		return self::$countries;
	}


	/**
	 * Get the country by its code.
	 *
	 * @param string $code The country code.
	 *
	 * @return string
	 */
	public static function get_country( $code ) {

		return ( ! empty( self::$countries[ $code ] ) ) ? self::$countries[ $code ] : '';
	}

	/**
	 * Get the code of a country.
	 *
	 * @param string $country The country name.
	 *
	 * @return string
	 */
	public static function get_code( $country ) {

		$codes = array_flip( self::$countries );
		return ( ! empty( $codes[ $country ] ) ) ? $codes[ $country ] : '';
	}
}
