jQuery(document).on("blur", "#tracking_number", function(){	
	var arr = [];
	var number = jQuery(this).val().replaceAll(/[^\w\s]/gi, '').replaceAll(" ", "");	
	
	// USPS
	if ( number.match(/^EE\d{9}AE$|^92\d{20}$|^94\d{20}$|^93\d{24}$|^LH\d{9}US$|^92\d{24}$|^420\d{27}$|^EE\d{9}IL$|^95\d{20}$|^420\d{31}$/, '') ) {
		arr = jQuery.merge( arr, [{"usps": "USPS"}] ); //9400111202508825522526
	}

	// UPS
	if ( number.match(/^1Z\w{9}\d{7}$|^927\d{23}$/, '') ) {
		arr = jQuery.merge( arr,[{"ups": "UPS"}]); //1Z96F4R8D313969574
	}

	//Fedex
	if ( number.match(/^289\d{9}$|^287\d{9}$|^282\d{9}$|^775\d{9}$|^774\d{9}$|^6129\d{16}$|^55\d{10}$|^009\d{11}$|^54\d{10}$|^56\d{10}$|^50\d{10}$|^9261\d{18}$|^131\d{9}$|^45\d{10}$|^2766\d{8}$|^777\d{9}$|^27\d{10}$|^53\d{10}$|^30\d{13}$|^39\d{10}$|^61\d{10}$|^77\d{10}$|^63\d{10}$|^60\d{10}$|^99\d{13}$|^29\d{13}$|^64\d{10}$|^29266\d{10}$|^64\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"fedex": "Fedex"}]); //775817441109
	}
	//Fedex FIMS
	if ( number.match(/^51\d{10}$|^775\d{9}$|^948\d{9}$|^282\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"fedex-fims": "Fedex FIMS"}]); //515947531160
	}
	//Fedex Freight
	if ( number.match(/^289\d{9}$|^288\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"fedex-freight": "Fedex Freight"}]); //288844052870
	}
	//Fedex Ground
	if ( number.match(/^289\d{9}$|^288\d{9}$|^0129\d{11}$|^122\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"fedex-ground": "Fedex Ground"}]); //122092917167
	}

	// OSM Worldwide
	if ( number.match(/^927\d{19}$|^926\d{19}$/, '') ) {		
		arr = jQuery.merge( arr, [{"osmworldwide": "OSM Worldwide"}] ); //9274890314668400144507	
	}

	// TForce Logistics
	if ( number.match(/^CAYT\d{16}$/, '') ) {
		arr = jQuery.merge( arr, [{"tforce-logistic": "TForce Logistics"}] ); //CAYT2208621266016439		
	}

	// DSV
	if ( number.match(/^121\d{5}$|^202\d{5}$|^101\d{4}$|^112\d{4}$/, '') ) {
		arr = jQuery.merge( arr, [{"dsv": "DSV"}] ); //12123443, 20220326
	}

	// Yun Express Tracking
	if ( number.match(/^YT\d{16}$/, '') ) {
		arr = jQuery.merge( arr, [{"yunexpress": "Yun Express Tracking"}] ); //YT2203621272033099
	}

	//GLS US
	if ( number.match(/^0000000\d{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-us": "GLS US"}]); //00000001642547814271
	}

	//DHL Germany
	if ( number.match(/^35\d{10}$|^CU\d{9}DE$|^CB\d{9}DE$|^LY\d{9}DE$|^CS\d{9}DE$|^003\d{17}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-de": "DHL Germany"}]); //4026419596
	}

	//360lion Express
	if ( number.match(/^(WSH[A-Z]{2}\d{10}YQ)$/, '') ) {
		arr = jQuery.merge( arr,[{"360lion-express": "360lion Express"}]);
	}

	//4px
	if ( number.match(/^(LP00\d{12})$|^3042\d{8}$|^4PX\d{13}CN$/, '') ) {
		arr = jQuery.merge( arr,[{"4px": "4px"}]); // LP00492755414105/304200631500
	}

	//7-ELEVEN
	if ( number.match(/^(F\d{11})$/, '') ) {
		arr = jQuery.merge( arr,[{"qi-eleven": "7-ELEVEN"}]); // F85982005767
	}

	//99minutos
	if ( number.match(/^36\d{8}$|^39\d{8}$|^29\d{8}$|^25\d{8}$|^13\d{8}$|^16\d{8}$|^18\d{8}$|^41\d{8}$|^10\d{8}$|^17\d{8}$|^22\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"99minutos": "99minutos"}]); // 3990740637
	}

	//AAA Cooper
	if ( number.match(/^(22\d{7})$|^(21\d{7})$^(23\d{7})$/, '') ) {
		arr = jQuery.merge( arr,[{"aaa-cooper": "AAA Cooper"}]); // 234657609
	}

	//ABF
	if ( number.match(/^13\d{7}$|^07\d{7}$|^22\d{7}$|^06\d{8}/, '') ) {
		arr = jQuery.merge( arr,[{"abf": "ABF"}]); // 222120359
	}

	//ACS Courier
	if ( number.match(/^(49\d{8})$|^(31\d{8})$|^(55\d{8})$/, '') ) {
		arr = jQuery.merge( arr,[{"acs-courier": "ACS Courier"}]); // 4969907294
	}

	//Airpak Express
	if ( number.match(/^(100\d{9})$/, '') ) {
		arr = jQuery.merge( arr,[{"airpak-express": "Airpak Express"}]); // 100903291370
	}

	//Airwings
	if ( number.match(/^202(\d{9})$/, '') ) {
		arr = jQuery.merge( arr,[{"airwings": "Airwings"}]); // 202316200575
	}

	//Amazon IN
	if ( number.match(/^276\d{9}$|^2785\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"amazon-in": "Amazon IN"}]); // 276402388231
	}

	//Amazon Logistics
	if ( number.match(/^(TBA\w{12})$/, '') ) {
		arr = jQuery.merge( arr,[{"amazon-logistics": "Amazon Logistics"}]); // TBA115177973904
	}

	//Amazon UK
	if ( number.match(/^(QA\d{10})$/, '') ) {
		arr = jQuery.merge( arr,[{"amazon-uk": "Amazon UK"}]); // QA1828801035
	}

	//An Post
	if ( number.match(/^RK{2}\d{9}IE$|^CE{2}\d{9}IE$/, '') ) {
		arr = jQuery.merge( arr,[{"an-post": "An Post"}]); // RK801660960IE
	}
	
	//APC
	if ( number.match(/^(181750\d{10})$/, '') ) {
		arr = jQuery.merge( arr,[{"apc": "APC"}]); // 1817501200009121
	}

	//Aramex
	if ( number.match(/^46\d{9}$|^47\d{9}$|^3285\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"aramex": "Aramex"}]); // 47365489710
	}
	
	//Aramex AU
	if ( number.match(/^2X\d{10}$|^IZ\d{10}$|^FZ\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"aramex-au": "Aramex AU"}]); // 2X0008909201
	}
	
	//Aramex NZ
	if ( number.match(/^FX\d{10}$|^MX\d{10}$|^JQ\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"aramex-nz": "Aramex NZ"}]); // FX0000464375
	}
	
	//Global Order Tracking
	if ( number.match(/^LP\d{14}$|^6A\d{11}|^SD\d{9}FR$|^LB\d{9}HK$|^NL\d{9}BR$|^LZ\d{9}FR$|^LS\d{9}CH$|^LD\d{9}BE$|^C800\d{13}$|^LX\d{9}CN$/, '') ) {
		arr = jQuery.merge( arr,[{"global-order-tracking": "Global Order Tracking"}]); // NL044620100BR
	}
	
	//Asendia
	if ( number.match(/^210\d{15}$|^UM\d{9}US$|^00000000\d{7}|^UM\d{9}U$|^LK\d{9}FR$|^LF\d{9}FR$|^CY\d{9}US$|^RL\d{9}CH$|^6A\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"asendia": "Asendia"}]); // LF030796530FR / UM863132662US
	}
	
	//Asendia Germany
	if ( number.match(/^LF\d{9}FR$/, '') ) {
		arr = jQuery.merge( arr,[{"asendia-germany": "Asendia Germany"}]); // LF030796530FR
	}
	
	//Asendia USA
	if ( number.match(/^1805002610000\d{5}$|^6A\d{11}$|^[A-Z]{21}$|^CY\d{9}US$/, '') ) {
		arr = jQuery.merge( arr,[{"asendia-usa": "Asendia USA"}]); //180500261000033822 / 6A22770022266 /YPKFPRNFVUTYNWHIUMDQW / CY310253360US
	}
	
	//Australia EMS
	if ( number.match(/^EJ\d{9}AU$|^9970\d{10}004300909$|^[A-Z]{2}\d{14}$/, '') ) {
		arr = jQuery.merge( arr,[{"australia-ems": "Australia EMS"}]); //EJ287071520AU //99705173132901004300909 // LP00473000221080
	}
	
	//Australia Post
	if ( number.match(/^PR\d{8}$|^42R500\d{4}$|^997\d{20}$|^997\d{15}$|^33XF\d{8}$|^00000\d{5}$|^020705557333\d{9}$|^UCD\d{18}$|^33[A-Z]{3}\d{18}$|^020\d{18}$|^030\d{19}$|^33T7H\d{18}$|^R5\d{23}$|^L[A-Z]\d{9}AU$|^R\d{24}$|^R23\d{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"australia-post": "Australia Post"}]); //42R5007686
	}

	//Bangladesh EMS
	if ( number.match(/^[A-Z]{2}\d{9}BD$/, '') ) {
		arr = jQuery.merge( arr,[{"bangladesh-ems": "Bangladesh EMS"}]); //CP404601236BD
	}

	//BEST Express
	if ( number.match(/^60\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"best-express": "BEST Express"}]); //60850136322011
	}
	
	//BH Posta
	if ( number.match(/^RU\d{9}NL$/, '') ) {
		arr = jQuery.merge( arr,[{"bh-posta": "BH Posta"}]); //RU949714735NL
	}
	
	//Bluecare Express
	if ( number.match(/^BC[A-Z]{3}\d{10}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"bluecare": "Bluecare Express"}]); //BCECH2286505800YQ
	}
	
	//Bluedart
	if ( number.match(/^50\d{9}$|^89\d{9}$|^78\d{9}$|^160\d{7}$|^20\d{9}$|^15\d{9}$|^42\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"bluedart": "Bluedart"}]); //89377588650
	}

	//Bombino Express
	if ( number.match(/^39\d{5}$/, '') ) {
		arr = jQuery.merge( arr,[{"bombino-express": "Bombino Express"}]); //3978837
	}	
	//Border Express
	if ( number.match(/^DCMC\d{6}$|^MTLM\d{6}|^BCWS\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"border-express": "Border Express"}]); //DCMC002286
	}	
	//Quantium
	if ( number.match(/^LQ\d{9}SG$|^RQ\d{9}SG$/, '') ) {
		arr = jQuery.merge( arr,[{"quantium": "Quantium"}]); //RQ310408089SG /
	}	
	//Bpost
	if ( number.match(/^L[A-Z]\d{9}BE$|^ce\d{9}be$|^3232\d{20}$|^R[A-Z]\d{9}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"belgium-post": "Bpost"}]); //LY332288710DE / 323211383300001502624030
	}	
	//Brazil Correios
	if ( number.match(/^O[A-Z]\d{9}BR$|Q[A-Z]\d{9}BR$|^N[A-Z]\d{9}BR$|^LP\d{14}$|^LE\d{9}SE$|^LB\d{9}HK$/, '') ) {
		arr = jQuery.merge( arr,[{"brazil-correios": "Brazil Correios"}]); //OS749364345BR
	}	
	//Bring
	if ( number.match(/^73\d{15}$|^37\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"bring": "Bring"}]); //73325389904349843
	}	
	//BRT
	if ( number.match(/^09\d{10}$|^10\d{10}$|^17\d{10}$|^11\d{10}$|^08\d{10}$|^15\d{10}$|^14\d{13}$|^13\d{13}$|^14\d{10}$|^078\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"brt": "BRT"}]); //92010016295
	}	
	//Bulgaria Post
	if ( number.match(/^RI\d{9}BG$/, '') ) {
		arr = jQuery.merge( arr,[{"bulgaria-post": "Bulgaria Post"}]); 
	}	
	//Cainiao
	if ( number.match(/^128\d{23}$|^71\d{10}$|^132\d{23}$|^42\d{32}$|^6A\d{11}$|^QP\d{9}GB$|^NX\d{9}BR$|^[A-Z]\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"aliexpress-standard-shipping": "Cainiao"}]);  //12876900007000242694867001
	}	
	//Chunghwa Post
	if ( number.match(/^^RG\d{9}TW$|^LX\d{9}TW$|^EG\d{9}TW$|^CG\d{9}TW$|^RA\d{9}TW$|^52\d{18}$|^51\d{18}$|^184\d{17}$|^\d{20}$/, '') ) {
		arr = jQuery.merge( arr,[{"taiwan-post": "Chunghwa Post"}]); //52066290010170403006
	}	
	//Canada Post
	if ( number.match(/^97\d{14}$|^96\d{14}$|^06\d{14}$|^83\d{14}$|^84\d{14}$|^98\d{14}$|^10\d{14}|^80\d{14}$|^[A-Z]{2}\d{9}CA$|^20\d{14}$|^EE\d{9}AE$|^E[A-Z]\d{9}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"canada-post": "Canada Post"}]); //PG616083375CA
	}	
	//Canpar
	if ( number.match(/^D\d{21}$/, '') ) {
		arr = jQuery.merge( arr,[{"canpar": "Canpar"}]); //D424012110000015233001
	}	
	//CBL Logistics
	if ( number.match(/^\w{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"cbl-logistica": "CBL Logistics"}]);  //68DA38F3
	}	
	//CDEK
	if ( number.match(/^13\d{8}$|^12\d{8}$|^[A-Z]{2}\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"cdek": "CDEK"}]);  //1303701129
	}	
	//Central Transport
	if ( number.match(/^14\d{9}$|^15\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"central-transport": "Central Transport"}]);  //14965259519
	}	
	//Ceska Posta
	if ( number.match(/^RC\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"ceska-posta": "Ceska Posta"}]);  //RC598233734DE
	}	
	//CEVA Logistics
	if ( number.match(/^([A-Z]{2}\d{6})$/, '') ) {
		arr = jQuery.merge( arr,[{"ceva-logistics": "CEVA Logistics"}]);  //AY059298
	}	
	//China Post
	if ( number.match(/^R[A-Z]\d{9}CN|V[A-Z]\d{9}CN|A[A-Z]\d{9}CN|C[A-Z]\d{9}CN|XA\d{10}$|^L[A-Z]\d{9}CN/, '') ) {
		arr = jQuery.merge( arr,[{"china-post": "China Post"}]);  //AG904312272CN
	}
	//Chit Chats
	if ( number.match(/^[A-Z]\d[A-Z]\d[A-Z]\d[A-Z]\d[A-Z]\d$/, '') ) {
		arr = jQuery.merge( arr,[{"chit-chats": "Chit Chats"}]);  //T2N9Q1H7I9
	}
	//Chronopost
	if ( number.match(/^XW\d{9}JB$|^X[A-Z]\d{9}[A-Z]{2}$|^x[a-z]\d{9}[a-z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"chronopost": "Chronopost"}]); //XW435801688JB
	}
	//City Express
	if ( number.match(/^68800\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"city-express": "City Express"}]);  //6880021515759108
	}
	//City-Link Express
	if ( number.match(/^06030\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"city-link-express": "City-Link Express"}]);  //060301854002706
	}
	//CJ Logistics
	if ( number.match(/^384\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"cj-logistics": "CJ Logistics"}]);  //384262703104*
	}
	//Sunyou
	if ( number.match(/^SY[A-Z]{2}\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"sunyou": "Sunyou"}]);  //SYUS006662718
	}
	//CJPacket
	if ( number.match(/^CJP[A-Z]{2}\d{10}YQ$|^00\d{18}$|^\d[A-Z]\d{11}$|^YT\d{16}$|^[A-Z]{3}\d[A-Z]{2}\d{13}$|^420\d{31}$|^[A-Z]{4}\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"cj-packet": "CJPacket"}]);  //CJPNX0210202151YQ
	}
	//CNE Express
	if ( number.match(/^3A5V\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"cne-express": "CNE Express"}]);  //3A5V599592327
	}
	//Colis Privé
	if ( number.match(/^N9\d{15}$|^C800\d{13}$|^1R\d{14}$/, '') ) {
		arr = jQuery.merge( arr,[{"colis-prive": "Colis Privé"}]);  //C8001646267606500
	}
	//La Poste
	if ( number.match(/^R[A-Z]\d{9}FR$|^V[A-Z]\d{9}FR$|^A[A-Z]\d{9}FR$|^\d[A-Z]\d{11}$|^L[A-Z]\d{9}FR$/, '') ) {
		arr = jQuery.merge( arr,[{"la-poste": "La Poste"}]);  //1Z00050675661
	}
	//colissimo
	if ( number.match(/^\d[A-Z]\d{11}$|^CU\d{9}DE$|^C[A-Z]\d{9}FR$|^C\d{16}$|^L[A-Z]\d{9}FR$/, '') ) {
		arr = jQuery.merge( arr,[{"colissimo": "colissimo"}]);  //1Z00052788154
	}
	//CollectPlus
	if ( number.match(/^87MY\w{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"collectplus": "CollectPlus"}]);  //87MY19746516A008
	}
	//Colombia Post
	if ( number.match(/^(R[A-Z]\d{9}CO)$|^(V[A-Z]\d{9}CO)$|^(A[A-Z]\d{9}CO)$|^(C[A-Z]\d{9}CO)$|^(E[A-Z]\d{9}CO)$|^(L[A-Z]\d{9}CO)$|^(IP\d{6}CO)$|(IP\d{9}CO)|(^\d{6}$)|^ML\d{9}MH$/, '') ) {
		arr = jQuery.merge( arr,[{"colombia-post": "Colombia Post"}]); //ML103251513MH
	}
	//Comet Hellas
	if ( number.match(/^\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"cometcourier": "Comet Hellas"}]); //54290549*
	}
	//Coordinadora
	if ( number.match(/^(\d{11})$/, '') ) {
		arr = jQuery.merge( arr,[{"coordinadora": "Coordinadora"}]); //58610008691*
	}
	//Correos Chile
	if ( number.match(/^RC\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"correos-chile": "Correos Chile"}]); //RC598436604DE*
	}
	//Correos de Cuba
	if ( number.match(/^EO00\d{7}AE$|^CP00\d{7}BA$/, '') ) {
		arr = jQuery.merge( arr,[{"correos-de-cuba": "Correos de Cuba"}]); //EO000204803AE
	}
	//Correos España
	if ( number.match(/^P[A-Z]\d[A-Z]\d[A-Z]\d{16}[A-Z]$|^L[A-Z]\d{9}CN$|^RF\d{9}ES$|^RR\d{9}PL$/, '') ) {
		arr = jQuery.merge( arr,[{"correos-spain": "Correos España"}]); //PK6Q1D0710032430128440H
	}
	//Correos Express
	if ( number.match(/^32\d{14}$|^66\d{14}$|^32\d{21}$|^[A-Z]{3}\d{5}$/, '') ) {
		arr = jQuery.merge( arr,[{"correos-express": "Correos Express"}]); //6630004760384438
	}
	//Courier IT
	if ( number.match(/^\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"courier-it": "Courier IT"}]); //58704916*
	}
	//CourierPost
	if ( number.match(/^00\d{18}$|^\d{16}[A-Z]{3}00\d[A-Z]{2}$|^29\d{14}$/, '') ) {
		arr = jQuery.merge( arr,[{"courierpost": "CourierPost"}]);  //00794210320413344113
	}
	//CouriersPlease
	if ( number.match(/^CP\w{12}$|^CP\w{15}$|^CP\w{14}$|^cp\w{15}$|^13\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"couriersplease": "CouriersPlease"}]);  // CPWAGVBK000122073 / 13504227394
	}
	//Croatia Post
	if ( number.match(/^[A-Za-z]{2}\d{9}HR$/, '') ) {
		arr = jQuery.merge( arr,[{"croatia-post": "Croatia Post"}]);  //RF888475371HR
	}
	//CTT Express
	if ( number.match(/^009\d{22}$|^009\d{19}$/, '') ) {
		arr = jQuery.merge( arr,[{"ctt-express": "CTT Express"}]);  //0095590095599511071312
	}
	//Cubyn
	if ( number.match(/^CUB\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"cubyn": "Cubyn"}]);  //CUB679908404*
	}
	//Cyprus Post
	if ( number.match(/^[A-Z]{2}\d{9}CY$/, '') ) {
		arr = jQuery.merge( arr,[{"cyprus-post": "Cyprus Post"}]);  //RQ028918481CY
	}
	//Czech Post
	if ( number.match(/^[A-Z]{2}\d{9}CZ$/, '') ) {
		arr = jQuery.merge( arr,[{"czech-post": "Czech Post"}]);  //RR950034580CZ
	}
	//Dachser
	if ( number.match(/^90\d{11}$|^\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dachser": "Dachser"}]);  //9052003296262
	}
	//Dai Post
	if ( number.match(/^[A-Z]{2}\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"dai-post": "Dai Post"}]);  //HG1000263662
	}
	//DAO
	if ( number.match(/^000\d{17}$/, '') ) {
		arr = jQuery.merge( arr,[{"dao": "DAO"}]);  //00057151271003022857*
	}
	//Dawn Wing
	if ( number.match(/^MIN\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"dawn-wing": "Dawn Wing"}]);  //MIN2220024560
	}
	//Day & Ross
	if ( number.match(/^[A-Z]{3}\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"dayross": "Day & Ross"}]);  //SMI9128331
	}
	//Day & Ross
	if ( number.match(/^[A-Z]{3}\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"dayross": "Day & Ross"}]);  //SMI9128331
	}
	//DPD UK
	if ( number.match(/^554\d{7}$|^159\d{11}$|^155\d{11}$|^24\d{8}$|^202\d{7}$|^685\d{7}$|^155\d{11}[A-Z]$|^159\d{11}[A-Z]$|^198\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-uk": "DPD UK"}]);  //15976855333968
	}
	//DB Schenker
	if ( number.match(/^80\d{16}$|^15\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"db-schenker": "DB Schenker"}]);  //15690068844986
	}
	//Delhivery
	if ( number.match(/^12\d{12}$|^399\d{10}$|^19\d{12}$|^24\d{11}$|^68\d{11}$|^80\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"delhivery": "Delhivery"}]);  //6860010317590
	}
	//Deltec Courier
	if ( number.match(/^71\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"deltec-courier": "Deltec Courier"}]);  //711913364330
	}
	//Denmark Post
	if ( number.match(/^RR\d{9}PL$|^RN\d{9}GB$|^00\d{18}$|^13\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"denmark-post": "Denmark Post"}]);  //RR962541924PL
	}
	//Deppon
	if ( number.match(/^DP[A-Z]\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"deppon": "Deppon"}]);  //DPK200071957168
	}
	//Deutsche Post
	if ( number.match(/^LB\d{9}DE$|^R[A-Z]\d{9}DE$|^R[A-Z]\d{9}PL$|^R[A-Z]\d{9}GB$|^A\d{5}[A-Z]{2}\d{11}[A-Z]$|^A\d{5}[A-Z]{2}\d{12}$|^L[A-Z]\d{9}GB$/, '') ) {
		arr = jQuery.merge( arr,[{"deutsche-post": "Deutsche Post"}]);
	}
	//Deutsche Post DHL
	if ( number.match(/^003\d{17}$|^LB\d{9}DE$|^CA\d{9}DE$|^JJD\d{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"deutsche-post-dhl": "Deutsche Post DHL"}]); //JJD1405839213049
	}	
	//DHL at
	if ( number.match(/^\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-at": "DHL at"}]); //100001
	}
	//DHL eCommerce
	if ( number.match(/^420\d{27}$|^GM\d{18}$|^L[A-Z]\d{9}DE$|^7322\d{12}$|^RX\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-ecommerce": "DHL eCommerce"}]); //420113859374869903508141282867
	}
	//DHL Express
	if ( number.match(/^191\d{7}$|^83\d{8}$|^[A-Z]{4}\d{20}$|^003\d{17}$|^12\d{8}$|^30\d{8}$|^68\d{8}$|^55\d{8}$|^74\d{8}$|^56\d{8}$|^20\d{8}$|^49\d{8}$|^63\d{8}$|^96\d{8}$|^15\d{8}$|^22\d{8}$|^98\d{8}$|^34\d{8}$|^43\d{8}$|^36\d{8}$|^79\d{8}$|^21\d{8}$|^73\d{8}$|^76\d{8}$|^78\d{8}$|^91\d{8}$|^111\d{7}$|^94\d{8}$|^617\d{7}$|^46\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-express": "DHL Express"}]); //1042924260
	}
	//DHL Express UK
	if ( number.match(/^34\d{8}$|^30\d{8}$|^89\d{8}$|^43\d{8}$|^19\d{8}$|^56\d{8}$|^18\d{8}$|^24\d{8}$|^14\d{8}$|^38\d{8}$|^13\d{8}$|^75\d{8}$|^77\d{9}$|^27\d{8}$|^74\d{8}$|^75\d{8}$|^29\d{8}$|^81\d{8}$|^29\d{8}$|^79\d{8}$|^97\d{8}$|^64\d{8}$|^35\d{8}$|^47\d{8}$|^32\d{8}$|^70\d{8}$|^10\d{8}$|^17\d{8}$|^68\d{8}$|^28\d{8}$|^92\d{8}$|^12\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-uk": "DHL Express UK"}]); //3436503836
	}
	//DHL Freight
	if ( number.match(/^42\d{7}$|^13\d{8}$|^38\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-freight": "DHL Freight"}]); //1342141813
	}
	//DHL Paket
	if ( number.match(/^CU\d{9}DE$|^JD\d{18}$|^003\d{17}$|^CQ\d{9}DE$|^CA\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-paket": "DHL Paket"}]); //CU707538487DE
	}
	//DHL Parcel
	if ( number.match(/^MYEOJ\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-parcel": "DHL Parcel"}]); //MYEOJ3117551643332054
	}
	//DHL Parcel Spain
	if ( number.match(/^JVGL\d{18}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-parcel-se": "DHL Parcel Spain"}]); //JVGL106871000971591823
	}
	//DHL Parcel UK
	if ( number.match(/^419\d{11}$|^601\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-parcel-uk": "DHL Parcel UK"}]); //41708870017135
	}
	//DHL Poland
	if ( number.match(/^JJD\d{21}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-pl": "DHL Poland"}]); //JJD149020300609000000582
	}	
	//DHL se
	if ( number.match(/^70\d{8}$|^51\d{8}$|^72\d{8}$|^73\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-se": "DHL se"}]); //7051823505
	}
	//DHL Spain
	if ( number.match(/^JD\d{18}$|^JJD\d{20}$|^461\d{7}$|^59\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-es": "DHL Spain"}]); //5902872765
	}
	//DHL US
	if ( number.match(/^420\d{27}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhl-us": "DHL US"}]); //420820099361269903508126810683
	}
	//DHLParcel NL
	if ( number.match(/^JVGL\d{20}$|^JJD\d{22}$|^HL\d{9}JB$|^3SDFC\d{10}$|^JVGL\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"dhlParcel-nl": "DHLParcel NL"}]); //JVGL0617326433928430
	}
	//Dicom
	if ( number.match(/^W\d{7}$|^W\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dicom": "Dicom"}]); //W62974435
	}
	//Direct Freight
	if ( number.match(/^3366\d{9}$|^34207\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"direct-freight": "Direct Freight"}]); //3366515188733
	}
	//Direct Link
	if ( number.match(/^LB\d{9}SE$/, '') ) {
		arr = jQuery.merge( arr,[{"direct-link": "Direct Link"}]); //LB795160415SE
	}
	//Dotzot
	if ( number.match(/^I\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"dotzot": "Dotzot"}]); //I30023099506
	}	
	//DPD Austria
	if ( number.match(/^0622\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-at": "DPD Austria"}]); //06225028605590
	}
	//DPD Croatia
	if ( number.match(/^1750202\d{7}$|^0104\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-croatia": "DPD Croatia"}]); //17502024174372
	}
	//DPD Czech Republic
	if ( number.match(/^39\d{6}$|^40\d{10}$|^16\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-cz": "DPD Czech Republic"}]); //39627491
	}
	//DPD France
	if ( number.match(/^062\d{11}$|^130\d{11}$|^13\d{12}$|^250\d{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-fr": "DPD France"}]); //06215107428351
	}
	//DPD Germany
	if ( number.match(/^01\d{12}$|^05\d{12}$|^06\d{12}$|^08\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-de": "DPD Germany"}]); //05132040653892
	}
	//DPD Hungary
	if ( number.match(/^1640\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-hu": "DPD Hungary"}]); //16407427732219
	}	
	//DPD Ireland
	if ( number.match(/^860\d{5}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-ie": "DPD Ireland"}]); //860619167
	}
	//DPD Latvia
	if ( number.match(/^0575\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-lv": "DPD Latvia"}]); //05757961131335
	}
	//DPD Local
	if ( number.match(/^159\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-local": "DPD Local"}]); //15976855318164
	}
	//DPD Netherlands
	if ( number.match(/^05\d{12}$|^08\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-nl": "DPD Netherlands"}]); //05132019892402
	}
	//DPD Poland
	if ( number.match(/^1000\d{9}U$|^1348\d{10}$|^\d{13}[A-Z]\d$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-pl": "DPD Poland"}]); //1000402103190U
	}
	//DPD Portugal
	if ( number.match(/^095\d{11}[A-Z]$|^095\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-pt": "DPD Portugal"}]); //09599003049519Y
	}
	//DPD Romania
	if ( number.match(/^80\d{9}$|^223\d{6}$|^224\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-romania": "DPD Romania"}]); //223767343
	}
	//DPD Slovakia
	if ( number.match(/^065050\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-sk": "DPD Slovakia"}]); //06505070157356
	}
	//DPD Slovenia
	if ( number.match(/^062\d{11}$|^169\d{11}$|^05\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpd-si": "DPD Slovenia"}]); //06215107427532
	}
	//DPEX
	if ( number.match(/^500\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"dpex": "DPEX"}]); //500725487603
	}
	//DTDC
	if ( number.match(/^[A-Z]\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dtdc": "DTDC"}]); //D69988589
	}											
	//DTDC Plus
	if ( number.match(/^[A-Z]\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"dtdc-plus": "DTDC Plus"}]); //M05565361
	}
	//DX delivery
	if ( number.match(/^157\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"dx-delivery": "DX delivery"}]); //1572633007
	}
	//Echo
	if ( number.match(/^47\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"echo": "Echo"}]); //47589509
	}
	//Ecom Express
	if ( number.match(/^90\d{8}$|^91\d{8}$|^62\d{7}$|^65\d{7}$|^29\d{8}$|^28\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"ecom-express": "Ecom Express"}]); //9087094134
	}
	//Ekart
	if ( number.match(/^SRTP\d{10}$|^FMPP\d{10}$|^SRTC\d{10}$|^WSPP\d{10}$|^SNAC\d{10}$|^NMBP\d{10}$|^ITLC\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"ekart": "Ekart"}]); //SRTC4714232221
	}
	//ELTA Courier
	if ( number.match(/^RE\d{9}GR$|^PD\d{9}GR$|^HD\d{9}GR$|^ZG\d{9}GR$|^HA\d{9}GR$|^WG\d{9}GR$|^NI\d{9}GR$|^ZD\d{9}GR$|^ZL\d{9}GR$|^pd\d{9}gr$/, '') ) {
		arr = jQuery.merge( arr,[{"elta-courier": "ELTA Courier"}]); //PD380830197GR
	}
	//Emirates Post
	if ( number.match(/^10000\d{8}$|^EE\d{9}AE$/, '') ) {
		arr = jQuery.merge( arr,[{"emirates-post": "Emirates Post"}]); //EE505743454AE
	}
	//EMS
	if ( number.match(/^EV\d{9}CN$|^EB\d{9}CN$|^LY\d{9}CN$|^CY\d{9}CN$|^AS\d{9}CN$|^ev\d{9}cn$/, '') ) {
		arr = jQuery.merge( arr,[{"ems": "EMS"}]); //EV008712571CN
	}
	//ENVIALIA
	if ( number.match(/^00086\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"envialia": "ENVIALIA"}]); //0008630133063673
	}
	//ePacket
	if ( number.match(/^LV\d{9}CN$|^LP\d{9}CN$|^LZ\d{9}CN$/, '') ) {
		arr = jQuery.merge( arr,[{"epacket": "ePacket"}]); //LP176445020CN
	}
	//Estafeta
	if ( number.match(/^901\d{19}$|^101\d{19}$|^501\d{19}$|^705\d{19}$|^905\d{19}$|^105\d{19}$|^305\d{19}$|^205\d{19}$|^805\d{19}$|^405\d{19}$|^701\d{19}$|^605\d{19}$|^301\d{19}$|^201\d{19}|^00\d{20}$|^00\d{18}$/, '') ) {
		arr = jQuery.merge( arr,[{"estafeta": "Estafeta"}]); //0979915139 /  1015894550655608339947
	}
	//Estes
	if ( number.match(/^05\d{8}$|^16\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"estes": "Estes"}]); //1668059263
	}
	//Estonia Post
	if ( number.match(/^C[A-Z]\d{9}EE$|^R[A-Z]\d{9}EE$/, '') ) {
		arr = jQuery.merge( arr,[{"estonia-post": "Estonia Post"}]); //CE493984036EE
	}
	//Ethiopia Post
	if ( number.match(/^[A-Z]{2}\d{9}ET$/, '') ) {
		arr = jQuery.merge( arr,[{"ethiopia-post": "Ethiopia Post"}]); //CH204188001ET
	}
	//Expeditors
	if ( number.match(/^47\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"expeditors": "Expeditors"}]); //4730158643
	}
	//Express Courier
	if ( number.match(/^[A-Z]{2}\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"expresscourierintl": "Express Courier"}]); //YT2133821236040818
	}
	//Famiport
	if ( number.match(/^12\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"famiport": "Famiport"}]); //12090009612
	}
	//Faroe Islands Post
	if ( number.match(/^CI\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"faroe-islands": "Faroe Islands Post"}]); //CI981443037DE
	}
	//Fastway AU
	if ( number.match(/^[A-Z]{2}\d{10}$|^\d[A-Z]\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"fastway-au": "Fastway AU"}]); //RZ0007557351
	}
	//Fastway Ireland
	if ( number.match(/^[A-Z]{2}\d{10}$|^[A-Z]\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"fastway-ireland": "Fastway Ireland"}]); //X21000017065
	}
	//Fastway South Africa
	if ( number.match(/^[A-Z]{2}\d{10}$|^[A-Z]\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"fastway-za": "Fastway South Africa"}]); //OH0000079008
	}
	//FBA Swiship UK
	if ( number.match(/^[A-Z]{2}\d{10}$|^[A-Z]\d{4}[A-Z]\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"swiship-uk": "FBA Swiship UK"}]); //QB0165442075
	}
	//FBA Swiship USA
	if ( number.match(/^TBA\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"swiship-usa": "FBA Swiship USA"}]); //TBA436866298000
	}
	
	//Fetchr
	if ( number.match(/^34\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"fetchr": "Fetchr"}]); //34179733764762
	}
	//Finland Post
	if ( number.match(/^[A-Z]{2}FI\d{17}$|^0037\d{16}$|^R[A-Z]\d{9}DE$|^CE\d{9}FI$|^LD\d{9}BE$|^LB\d{9}DE$|^RU\d{9}NL$/, '') ) {
		arr = jQuery.merge( arr,[{"finland-post": "Finland Post"}]); //JJFI65229310040559968
	}
	//FirstMile
	if ( number.match(/^92\d{20}$/, '') ) {
		arr = jQuery.merge( arr,[{"firstmile": "FirstMile"}]); //9274890260129300071268
	}
	//Flash Express
	if ( number.match(/^TH\w{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"flash-express": "Flash Express"}]); //TH42102D96VH9Q
	}
	//Flyt Express
	if ( number.match(/^61\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"flyt-express": "Flyt Express"}]); //612198364988
	}
	//FMX
	if ( number.match(/^[A-Z]{3}\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"fmx": "FMX"}]); //CIT1010562897
	}
	//Gati
	if ( number.match(/^57\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gati": "Gati"}]); //570255246
	}
	//GDEX
	if ( number.match(/^MY\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"gdex": "GDEX"}]); //MY37069851972
	}
	//GEL Express
	if ( number.match(/^93\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"gel-express": "GEL Express"}]); //9323192676
	}
	//Geniki Taxydromiki
	if ( number.match(/^377\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"taxydromiki": "Geniki Taxydromiki"}]); //3775158715
	}
	//GEODIS
	if ( number.match(/^\d\w{9}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"geodis": "GEODIS"}]); //1GCJ3SGKW9BU
	}
	//GESWL Express
	if ( number.match(/^[A-Z]{5}\d{10}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"geswl": "GESWL Express"}]); //ZCEEU2012195151YQ
	}
	//Giao Hàng Nhanh
	if ( number.match(/^GAV\w{5}$/, '') ) {
		arr = jQuery.merge( arr,[{"ghn": "Giao Hàng Nhanh"}]); //GAV8XC7H
	}
	//Gibraltar Post
	if ( number.match(/^RU\d{9}NL$/, '') ) {
		arr = jQuery.merge( arr,[{"gibraltar-post": "Gibraltar Post"}]); //RU905291619NL
	}
	//Gig Logistics
	if ( number.match(/^103\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gig-logistics": "Gig Logistics"}]); //1031049537
	}
	//GLS Croatia
	if ( number.match(/^22\d{6}$|^21\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-croatia": "GLS Croatia"}]); //22757363
	}
	//GLS Denmark
	if ( number.match(/^91\d{9}$|^033\d{9}$|^058\d{9}|^YO\w{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-denmark": "GLS Denmark"}]); //33339879045
	}
	//GLS Europe
	if ( number.match(/^9723\d{10}$|^311\d{8}$|^2828\d{7}$|^9111\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls": "GLS Europe"}]); //31156685642
	}
	//GLS France
	if ( number.match(/^00F\w{5}$|^9111\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-france": "GLS France"}]); //00FMU9CQ
	}
	//GLS Ireland
	if ( number.match(/^9111\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-ireland": "GLS Ireland"}]); //91113952333
	}
	//GLS Italy
	if ( number.match(/^KS\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-italy": "GLS Italy"}]); //KS620026482
	}
	//GLS Netherlands
	if ( number.match(/^97\d{12}$|^755\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-netherlands": "GLS Netherlands"}]); //97230517213303
	}
	//GLS Paket
	if ( number.match(/^311\d{8}$|^ZPI\w{5}$|^ZP[A-Z]{6}$|^815\d{9}$|^2828\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-paket": "GLS Paket"}]); //31156685660
	}
	//GLS Slovenia
	if ( number.match(/^50\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-slovenia": "GLS Slovenia"}]); //502426455
	}
	//GLS Spain
	if ( number.match(/^9111\d{7}$|^4161\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gls-spain": "GLS Spain"}]); //91114051875
	}
	//GOGO Xpress
	if ( number.match(/^\d{8}[A-Z]{4}$/, '') ) {
		arr = jQuery.merge( arr,[{"gogo-xpress": "GOGO Xpress"}]); //8964-3265-DVKS
	}
	//GoJavas
	if ( number.match(/^65\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"gojavas": "GoJavas"}]); //650162445
	}
	//Grand Slam Express
	if ( number.match(/^888\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"grand-slam-express": "Grand Slam Express"}]); //8880036638
	}
	//Greenland Post
	if ( number.match(/^RR\d{9}DK$/, '') ) {
		arr = jQuery.merge( arr,[{"greenland-post": "Greenland Post"}]); //RR189427501DK
	}
	//GSO
	if ( number.match(/^GS\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"gso": "GSO"}]); //GS750440433
	}
	//HCT Logistics
	if ( number.match(/^67\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"hct": "HCT Logistics"}]); //6771368844
	}
	//Hermes
	if ( number.match(/^[A-Z]\d{2}[A-Z]{3}\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"hermes": "Hermes"}]); //H01HYA0005747846
	}
	//Hermes Germany
	if ( number.match(/^H\d{19}$|^14\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"hermes-de": "Hermes Germany"}]); //14032168300008 / H1002710741761101081
	}
	//HFD
	if ( number.match(/^32\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"hfd": "HFD"}]); //32957383
	}
	//Hong Kong Post
	if ( number.match(/^EA\d{9}HK$|^ea\d{9}hk$|^EE\d{9}HK$|^LH\d{9}HK$/, '') ) {
		arr = jQuery.merge( arr,[{"hong-kong-post": "Hong Kong Post"}]); //EA324101041HK
	}
	//Hunter Express
	if ( number.match(/^[A-Z]{3}\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"hunter-express": "Hunter Express"}]); //KLX064143
	}
	//Iceland Post
	if ( number.match(/^LF\d{9}FR$/, '') ) {
		arr = jQuery.merge( arr,[{"iceland-post": "Iceland Post"}]); //LF025343995FR
	}
	//India Post
	if ( number.match(/^[A-Z]{2}\d{9}IN$/, '') ) {
		arr = jQuery.merge( arr,[{"india-post": "India Post"}]); //RM737906273IN
	}
	//InPost Paczkomaty
	if ( number.match(/^643\d{21}$/, '') ) {
		arr = jQuery.merge( arr,[{"inpost-paczkomaty": "InPost Paczkomaty"}]); //643400489231636126555740
	}
	//Intelcom
	if ( number.match(/^INTL[A-Z]{3}\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"intelcom": "Intelcom"}]); //INTLCMC328940271
	}
	//Interparcel Au
	if ( number.match(/^AU\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"interparcel-au": "Interparcel Au"}]); //AU6028657309
	}
	//Interparcel uk
	if ( number.match(/^GB\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"interparcel-uk": "Interparcel Uk"}]); //GB5206089046
	}
	//IQfulfillment
	if ( number.match(/^IQ[A-Z]{4}\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"iq-fulfillment": "IQfulfillment"}]); //IQFSFM001402
	}
	//Israel Post
	if ( number.match(/^[A-Z]{2}\d{9}IL$/, '') ) {
		arr = jQuery.merge( arr,[{"israel-post": "Israel Post"}]); //EE089819227IL
	}
	//Ivory Coast EMS
	if ( number.match(/^\d{5}$/, '') ) {
		arr = jQuery.merge( arr,[{"ivory-coast-ems": "Ivory Coast EMS"}]); //17892
	}
	//J&T
	if ( number.match(/^J[A-Z]\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"j-t": "J&T"}]); //JD0152055622
	}
	//Jadlog
	if ( number.match(/^21\d{7}$|^20\d{7}$|^100\d{11}$|^19\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"jadlog": "Jadlog"}]); //200391805
	}
	//Janio
	if ( number.match(/^[A-Z]{3}\d{14}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"janio": "Janio"}]); //EAE22011613652426MY
	}
	//JCEX
	if ( number.match(/^JCE[A-Z]{2}\d{10}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"jcex": "JCEX"}]); //JCEUL1000047517YQ
	}
	//Jersey Post
	if ( number.match(/^[A-Z]{2}\d{9}JE$/, '') ) {
		arr = jQuery.merge( arr,[{"jersey-post": "Jersey Post"}]); //UM279642392JE
	}
	//JNE Express
	if ( number.match(/^71\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"jne": "JNE Express"}]); //UM279642392JE
	}
	//JoeyCo
	if ( number.match(/^JOEY[A-Z]{2}\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"joeyco": "JoeyCo"}]); //JOEYCO007924061
	}
	//JP Post
	if ( number.match(/^RM\d{9}IN$|^LP\d{9}CN$|^RZ\d{9}TW$|^RC\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"jp-post": "JP Post"}]); //RM003222783IN
	}
	//JS Express
	if ( number.match(/^JS[A-Z]{3}\d{10}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"js-express": "JS Express"}]); //JSESE2057015342YQ
	}
	//JT Express CN
	if ( number.match(/^JT\d{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"jt-express-cn": "JT Express CN"}]); //JT5091156768308
	}
	//JT Express MY
	if ( number.match(/^63\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"jt-express-my": "JT Express MY"}]); //630582541978
	}
	//JT Express PH
	if ( number.match(/^940\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"jt-express-ph": "JT Express PH"}]); //940441155457
	}
	//JT Express SG
	if ( number.match(/^JT\d{14}$/, '') ) {
		arr = jQuery.merge( arr,[{"jt-express-sg": "JT Express SG"}]); //JT20220209710225
	}
	//JT Express TH
	if ( number.match(/^821\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"jt-express-th": "JT Express TH"}]); //821797074556
	}
	//JT Express VN
	if ( number.match(/^80\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"jt-express-vn": "JT Express VN"}]); //801001320154
	}
	//Kerry eCommerce
	if ( number.match(/^KE[A-Z]{2}\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"kerry-ecommerce": "Kerry eCommerce"}]); //KECL600129788
	}
	//Kerry Express TH
	if ( number.match(/^KE[A-Z]\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"kerry-express-th": "Kerry Express TH"}]); //KEX20486575714
	}
	//Kerry Express VN
	if ( number.match(/^[A-Z]{2}\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"kerry-express-vn": "Kerry Express VN"}]); //HK974429
	}
	//Kerry Logistics
	if ( number.match(/^[A-Z]{4}\d{10}[A-Z]$/, '') ) {
		arr = jQuery.merge( arr,[{"kerry-logistics": "Kerry Logistics"}]); //FMSL0000271056A
	}
	//Korea Post
	if ( number.match(/^[A-Z]{15}\d{5}$/, '') ) {
		arr = jQuery.merge( arr,[{"korea-post": "Korea Post"}]); //CNSHAAKRSELBAUX20002
	}
	//Landmark Global
	if ( number.match(/^LTN\d{9}$|^IEN\d{9}[A-Z]\d$/, '') ) {
		arr = jQuery.merge( arr,[{"landmark-global": "Landmark Global"}]); //LTN180225153
	}
	//Laos Post
	if ( number.match(/^[A-Z]{5}\d{10}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"laos-post": "Laos Post"}]); //LAOUS0001913594YQ
	}
	//LaserShip
	if ( number.match(/^LS\d{8}$|^1LS\w{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"lasership": "LaserShip"}]); //LS12404347
	}
	//Latvia Post
	if ( number.match(/^L[A-Z]\d{9}LV$|^R[A-Z]\d{9}LV$|^V[A-Z]\d{9}LV$|^A[A-Z]\d{9}LV$|^C[A-Z]\d{9}LV$|^E[A-Z]\d{9}LV$/, '') ) {
		arr = jQuery.merge( arr,[{"latvia-post": "Latvia Post"}]); //LY034751043LV
	}
	//LBC Express
	if ( number.match(/^117\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"lbc-express": "LBC Express"}]); //1171838670
	}
	//Lexship
	if ( number.match(/^LA\d{9}EE$/, '') ) {
		arr = jQuery.merge( arr,[{"lexship": "Lexship"}]); //LA280971824EE
	}
	//Liechtenstein Post
	if ( number.match(/^RR\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"liechtenstein-post": "Liechtenstein Post"}]); //RR896691015DE
	}
	//Lone Star Overnight
	if ( number.match(/^[A-Z]{2}\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"lso": "Lone Star Overnight"}]); //DY505598
	}
	//Loomis Express
	if ( number.match(/^LSH[A-Z]\d{8}$|^HPX\w{9}$|^[A-Z]{3}\d{8}|^HN9\w{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"loomis-express": "Loomis Express"}]); //LSHP19992217
	}
	//LP Express
	if ( number.match(/^[A-Z]{2}\d{9}LT$/, '') ) {
		arr = jQuery.merge( arr,[{"lpexpress": "LP Express"}]); //EB961218160LT
	}
	//Luxembourg Post
	if ( number.match(/^LL\d{9}LU$/, '') ) {
		arr = jQuery.merge( arr,[{"luxembourg-post": "Luxembourg Post"}]); //LY333296187DE
	}
	//Macedonia Post
	if ( number.match(/^RB\d{9}SG$/, '') ) {
		arr = jQuery.merge( arr,[{"macedonia-post": "Macedonia Post"}]); //RB159608505SG
	}
	//Magyar Posta
	if ( number.match(/^R[A-Z]\d{9}HU$|^V[A-Z]\d{9}HU$|^A[A-Z]\d{9}HU$|^C[A-Z]\d{9}HU$|^E[A-Z]\d{9}HU$|^L[A-Z]\d{9}HU$|^PB\w{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"magyar-posta": "Magyar Posta"}]); //PB6SZ50018967
	}
	//Mail Boxed Etc
	if ( number.match(/^[A-Z]{2}\w{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"mbe": "Mail Boxed Etc"}]); //ES0148-0V-0000000EX8
	}
	//Main Freight
	if ( number.match(/^[A-Z]{3}\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"mainfreight": "Main Freight"}]); //MTL00399577
	}
	//Malta Post
	if ( number.match(/^RU\d{9}NL$/, '') ) {
		arr = jQuery.merge( arr,[{"malta-post": "Malta Post"}]); //LY319633762DE
	}
	//Meest
	if ( number.match(/^71\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"meest": "Meest"}]); //714-1098112
	}
	//Monaco EMS
	if ( number.match(/^RU\d{9}NL$/, '') ) {
		arr = jQuery.merge( arr,[{"monaco-ems": "Monaco EMS"}]); //RU872396035NL
	}
	//MRW
	if ( number.match(/^08\d{3}A\d{6}$|^08\d{10}$|^011\d{9}$|^02\d{3}A\d{6}$|^02\d{10}$|^0400\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"mrw": "MRW"}]); //02633A089977
	}
	//Mylerz
	if ( number.match(/^637\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"mylerz": "Mylerz"}]); //63771534998002
	}
	//Nampost
	if ( number.match(/^RB\d{9}SG$/, '') ) {
		arr = jQuery.merge( arr,[{"nampost": "Nampost"}]); //RB161082638SG
	}
	//Naqel
	if ( number.match(/^132\d{5}$|^111\d{5}$|^533\d{5}$|^110\d{5}$/, '') ) {
		arr = jQuery.merge( arr,[{"naqel": "Naqel"}]); //13248223
	}
	//New Zealand Post
	if ( number.match(/^4828\d{12}[A-Z]{3}\d{3}ON$|^LP\d{9}SG$|^007\d{17}$|^[A-Z]{2}\d{9}NZ$/, '') ) {
		arr = jQuery.merge( arr,[{"new-zealand-post": "New Zealand Post"}]); //HD035704186NZ
	}
	//Nexive
	if ( number.match(/^STCPA\d{15}$/, '') ) {
		arr = jQuery.merge( arr,[{"nexive": "Nexive"}]); //STCPAS00002121371498
	}
	//Nightline
	if ( number.match(/^[A-Z]{4}\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"nightline": "Nightline"}]); //PRSL18171835001
	}
	//Ninja Van
	if ( number.match(/^NVMY[A-Z]{2}\d{3}S\d{8}$|^NIN\w{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"ninja-van": "Ninja Van"}]); //NINSGLGN0F5S2IBD
	}
	//Ninja Van Malaysia
	if ( number.match(/^NVMY[A-Z]{2}\w{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"ninjavan-my": "Ninja Van Malaysia"}]); //NVMYUR123S40233278
	}
	//Ninja Van Philippines
	if ( number.match(/^[A-Z]{2}PH[A-Z]{2}\w{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"ninjavan-ph": "Ninja Van Philippines"}]); //NLPHMP0031335650
	}
	//NZ Couriers
	if ( number.match(/^ACOD\d{6}$|^SIN\d{6}$|^TGA\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"nz-couriers": "NZ Couriers"}]); //ACOD004006
	}
	//OCS Worldwide
	if ( number.match(/^84\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"ocs-worldwide": "OCS Worldwide"}]); //84780224244
	}
	//Old Dominion
	if ( number.match(/^778\d{8}$|^780\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"old-dominion": "Old Dominion"}]); //77839684121
	}
	//OnTrac
	if ( number.match(/^[A-Z]\d{14}$/, '') ) {
		arr = jQuery.merge( arr,[{"ontrac": "OnTrac"}]); //D10012696756908
	}
	//OrangeDS
	if ( number.match(/^42\d{28}$/, '') ) {
		arr = jQuery.merge( arr,[{"orangeds": "OrangeDS"}]); //420836869274890172373116685171
	}
	//Overseas Logistics
	if ( number.match(/^27\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"overseas-logistics": "Overseas Logistics"}]); //2750572322
	}
	//Overseas Territory US Post
	if ( number.match(/^[A-Z]{2}\d{18}$/, '') ) {
		arr = jQuery.merge( arr,[{"overseas-territory-us-post": "Overseas Territory US Post"}]); //GM545118533001742985
	}
	//Packlink
	if ( number.match(/^\w{19}$/, '') ) {
		arr = jQuery.merge( arr,[{"packlink": "Packlink"}]); //IT2021COM0001004965
	}
	//Pakistan Post
	if ( number.match(/^[A-Z]{2}\d{9}PK$/, '') ) {
		arr = jQuery.merge( arr,[{"pakistan-post": "Pakistan Post"}]); //RB089869206PK
	}
	//Palletways
	if ( number.match(/^600\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"palletways": "Palletways"}]); //60013498575
	}
	//Paquet Express
	if ( number.match(/^2111\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"paquet": "Paquet Express"}]); //211132724173
	}
	//Parcel2go
	if ( number.match(/^[Pp]2[Gg]\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"parcel2go": "Parcel2go"}]); //P2G96701166
	}
	//ParcelForce
	if ( number.match(/^[A-Z]{2}\d{7}$|^PB[A-Z]{2}\d{10}$|^CK\d{9}GB$|^P2G\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"parcelforce": "ParcelForce"}]); //PBPV7951955001
	}
	//PFC Express
	if ( number.match(/^35\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"pfcexpress": "PFC Express"}]); //357458910255
	}
	//Pgeon
	if ( number.match(/^\w{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"pgeon": "Pgeon"}]); //C762MW
	}
	//PHL Post
	if ( number.match(/^[A-Z]{2}\d{9}PH$/, '') ) {
		arr = jQuery.merge( arr,[{"phlpost": "PHL Post"}]); //RR089006072PH
	}
	//Pitney Bowes
	if ( number.match(/^[A-Z]{5}\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"pitney-bowes": "Pitney Bowes"}]); //PBXSA028532404
	}
	//Poczta Polska
	if ( number.match(/^[A-Z]{2}\d{9}PL$|^[A-Z]{2}\d{9}UA$/, '') ) {
		arr = jQuery.merge( arr,[{"poczta-polska": "Poczta Polska"}]); //RR424325444PL
	}
	//Portugal Post - CTT
	if ( number.match(/^[A-Z]{2}\d{9}PT$/, '') ) {
		arr = jQuery.merge( arr,[{"portugal-post-ctt": "Portugal Post - CTT"}]); //RH744129478PT
	}
	//Pos Indonesia
	if ( number.match(/^P211\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"pos-indonesia": "Pos Indonesia"}]); //P2111230113883
	}
	//PosLaju
	if ( number.match(/^[A-Z]{3}\d{9}MY$|^[A-Z]{2}\d{9}MY$/, '') ) {
		arr = jQuery.merge( arr,[{"poslaju": "PosLaju"}]); //ERC667915381MY
	}
	//Poslaju National
	if ( number.match(/^[A-Z]{3}\d{9}MY$|^[A-Z]{2}\d{9}MY$/, '') ) {
		arr = jQuery.merge( arr,[{"malaysia-post": "Poslaju National"}]); //ERC685449672MY
	}
	//Post Haste
	if ( number.match(/^AOBU\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"post-haste": "Post Haste"}]); //AOBU0005887
	}
	//Post One
	if ( number.match(/^RL\d{9}CH$/, '') ) {
		arr = jQuery.merge( arr,[{"postone": "Post One"}]); //RL925021690CH
	}
	//post.at
	if ( number.match(/^100022\d{16}|^103\d{19}$|^CD\d{9}AT$|^CD\d{9}AT$|^CA\d{9}AT$|^RN\d{9}GB$|^RR\d{9}PL$|^LB\d{9}DE$|^RC\d{9}DE$|^LV\d{9}CN$/, '') ) {
		arr = jQuery.merge( arr,[{"post-at": "post.at"}]); //1038118500035613902769
	}
	//Poste Italiane
	if ( number.match(/^\dA\d{4}I\d{6}|^1UW\d{10}$|^RG\d{9}UA$|^LB\d{9}DE|^RG\d{9}UA$|^RG\d{9}UA$|^5P34C\d{8}$|^RC\d{9}DE$|^RN\d{9}GB$|^RR\d{9}PL$|\d[A-Z]{3}\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"poste-italiane": "Poste Italiane"}]); //3C6075I298649
	}
	//Posten Norge
	if ( number.match(/^70\d{15}$|^LA\d{9}EE$|^RN\d{9}GB$|^37\d{16}$|^R[A-Z]\d{9}GB$|^R[A-Z]\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"posten-norge": "Posten Norge"}]); //LY322736055DE
	}
	//PostNL
	if ( number.match(/^3S[A-Z]{4}\d{9}$|^[A-Z]{2}\d{9}NL$|^3S[A-Z]{4}\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"postnl": "PostNL"}]); //3SOVAC7567398
	}
	//PostNL International 3S
	if ( number.match(/^3S[A-Z]{4}\d{9}$|^[A-Z]{2}\d{9}NL$|^3S[A-Z]{4}\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"postnl-international-3s": "PostNL International 3S"}]); //3SOPKA4499276
	}
	//PostNord Denmark
	if ( number.match(/^RU\d{9}NL$|^05\d{12}$|^003\d{17}$|^LV\d{9}CN$/, '') ) {
		arr = jQuery.merge( arr,[{"post-nord-denmark": "PostNord Denmark"}]); //LY333749506DE
	}
	//PostNord Norge
	if ( number.match(/^003\d{17}$|^70\d{15}$|^37\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"postnord-norge": "PostNord Norge"}]); //00373400306297064918
	}
	//PostNord Sverige AB
	if ( number.match(/^UI\d{9}SE$|^LX\d{9}ES$|^RS\d{9}DE$|^L[A-Z]\d{9}SE$|^RU\d{9}NL$/, '') ) {
		arr = jQuery.merge( arr,[{"postnord-sverige-ab": "PostNord Sverige AB"}]); //UI146288250SE
	}
	//PPL CZ
	if ( number.match(/^20\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"ppl-cz": "PPL CZ"}]); //20451516470
	}
	//Purolator
	if ( number.match(/^3333\d{8}$|^6079\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"purolator": "Purolator"}]); //333349271648
	}
	//Qxpress
	if ( number.match(/^QSP\d{9}$|^[A-Z]\d{7}[A-Z]{4}$/, '') ) {
		arr = jQuery.merge( arr,[{"qxpress": "Qxpress"}]); //C5855495SGCA
	}
	//Raben
	if ( number.match(/^120\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"raben": "Raben"}]); //120511216399940
	}
	//RAF Philippines
	if ( number.match(/^81\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"raf": "RAF Philippines"}]); //8120122032
	}
	//Redpack
	if ( number.match(/^033\d{6}$|^33\d{6}$|^78\d{7}$|^38\d{7}$|^97\d{6}$|^70\d{6}$|^57\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"redpack": "Redpack"}]); //033104760
	}
	//Redpack Mexico
	if ( number.match(/^37\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"redpack-mexico": "Redpack Mexico"}]); //37046815
	}
	//Redur Spain
	if ( number.match(/^25\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"redur-es": "Redur Spain"}]); //250595706
	}
	//RIVIGO
	if ( number.match(/^200\d{7}$|^100\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"rivigo": "RIVIGO"}]); //2001921559
	}
	//RL Carriers
	if ( number.match(/^95\d{7}$|^98\d{7}$|^36\d{7}$|^64\d{7}$|^72\d{7}$|^58\d{7}$|^04\d{7}$|^02\d{7}$|^87\d{7}$|^16\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"rl-carriers": "RL Carriers"}]); //954336222
	}
	//Romania Post
	if ( number.match(/^[A-Z]{2}\d{9}RO$/, '') ) {
		arr = jQuery.merge( arr,[{"posta-romana": "Romania Post"}]); //RN803049000RO
	}
	//Royal Mail
	if ( number.match(/^[A-Z]{2}\d{9}GB$/, '') ) {
		arr = jQuery.merge( arr,[{"royal-mail": "Royal Mail"}]); //TQ071537693GB
	}
	//RPX Indonesia
	if ( number.match(/^799\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"rpx": "RPX Indonesia"}]); //799980852706
	}
	//Russian Post
	if ( number.match(/^[A-Z]{2}\d{9}RU$|^80\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"russian-post": "Russian Post"}]); //EE058657590RU
	}
	//S.F Express
	if ( number.match(/^SF\d{14}$|^SF\d{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"s-f-express": "S.F Express"}]); //SF6043310798724
	}
	//Safexpress
	if ( number.match(/^97\d{6}$|^87\d{6}$|^96\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"safexpress": "Safexpress"}]); //97672607
	}
	//Sagawa
	if ( number.match(/^360\d{9}$|^35\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"sagawa": "Sagawa"}]); //360889315032
	}
	//Saia
	if ( number.match(/^105\d{9}$|^106\d{9}$|^770\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"saia": "Saia"}]); //105762504003
	}
	//SAP Express
	if ( number.match(/^[A-Z]{3}\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"sap-express": "SAP Express"}]); //ZAL00082363127
	}
	//Saudi Post
	if ( number.match(/^EK\d{9}SA$|^R[A-Z]\d{9}DE$|^RB\d{9}SG$/, '') ) {
		arr = jQuery.merge( arr,[{"saudi-post": "Saudi Post"}]); //EK129386481SA
	}
	//SDA
	if ( number.match(/^\dC\d{4}[A-Z]\d{6}$|^288\d{10}$|^\dUW\w{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"sda": "SDA"}]); //2883330043293
	}
	//Sendle
	if ( number.match(/^S\w{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"sendle": "Sendle"}]); //S65MDCD
	}
	//Serbia Post
	if ( number.match(/^[A-Z]{2}\d{9}RS$/, '') ) {
		arr = jQuery.merge( arr,[{"serbia-post": "Serbia Post"}]); //PX703566261RS
	}
	//Seur
	if ( number.match(/^[A-Z]{2}\d{4}[A-Z]{3}\d{10}$|^070\d{11}$|^88\d{5}$|^SEUR[A-Z]{3}\d{13}$|^[A-Z]{3}\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"seur": "Seur"}]); //SEURPRO2201152103697
	}
	//SF International
	if ( number.match(/^SF\d{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"sf-international": "SF International"}]); //SF1125346238243
	}
	//SFC Service
	if ( number.match(/^LV\d{9}CN$|^420\d{27}$|^LT\d{9}NL$|^2X\d{10}$|^WW\d{13}$|^RU\d{9}NL$|SFC\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"sfc-service": "SFC Service"}]); //420940229374869903507760982790
	}
	//Shadowfax
	if ( number.match(/^[A-Z]{2}\d{12}[A-Z]{3}$|^SF\d{9}KR$/, '') ) {
		arr = jQuery.merge( arr,[{"shadowfax": "Shadowfax"}]); //SF204430072443FPL
	}
	//Shipa
	if ( number.match(/^SD\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"shipa": "Shipa"}]); //SD004860712
	}
	//Shree Mahabali Express
	if ( number.match(/^10\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"shree-mahabali-express": "Shree Mahabali Express"}]); //1069966309
	}
	//Shree Maruti Courier
	if ( number.match(/^210\d{11}$|^211\d{11}$/, '') ) {
		arr = jQuery.merge( arr,[{"shree-maruti-courier": "Shree Maruti Courier"}]); //21014100094218
	}
	//Shree Tirupati Courier
	if ( number.match(/^101\d{9}$|^182\d{9}$|^104\d{9}$|^180\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"shree-tirupati-courier": "Shree Tirupati Courier"}]); //180100086521
	}
	//Sicepat
	if ( number.match(/^00\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"sicepat": "Sicepat"}]); //2847918441
	}
	//Singapore Post
	if ( number.match(/^L[A-Z]\d{9}SG$|^R[A-Z]\d{9}SG$/, '') ) {
		arr = jQuery.merge( arr,[{"singapore-post": "Singapore Post"}]); //LB270687462SG
	}
	//Singapore Speedpost
	if ( number.match(/^L[A-Z]\d{9}SG$|^R[A-Z]\d{9}SG$/, '') ) {
		arr = jQuery.merge( arr,[{"singapore-speedpost": "Singapore Speedpost"}]); //LP904647789SG
	}
	//Skynet
	if ( number.match(/^97\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"skynet": "Skynet"}]); //67100219163
	}
	//SkyNet Worldwide Express
	if ( number.match(/^016\d{9}$|^006\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"skynet-worldwide-express": "SkyNet Worldwide Express"}]); //6581036561
	}
	//Slovakia Post
	if ( number.match(/^[A-Z]{2}\d{9}SK$/, '') ) {
		arr = jQuery.merge( arr,[{"slovakia-post": "Slovakia Post"}]); //DA143949413SK
	}
	//Slovenia Post
	if ( number.match(/^[A-Z]{2}\d{9}SI$/, '') ) {
		arr = jQuery.merge( arr,[{"slovenia-post": "Slovenia Post"}]); //LH002505797SI
	}
	//SMSA Express
	if ( number.match(/^290\d{9}$|^210\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"smsa-express": "SMSA Express"}]); //290253637259
	}
	//South African Post Office
	if ( number.match(/^RC\d{9}DE$/, '') ) {
		arr = jQuery.merge( arr,[{"south-african-post-office": "South African Post Office"}]); //RC602364945DE
	}
	//Spee-Dee
	if ( number.match(/^SP\d{18}$|^SP\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"spee-dee": "Spee-Dee"}]); //SP001087030920178683
	}
	//Speedex Courier
	if ( number.match(/^7000\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"speedex-courier": "Speedex Courier"}]); //700011062832
	}
	//Spoton
	if ( number.match(/^70\d{7}$|^74\d{7}$|^53\d{7}$|^52\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"spoton": "Spoton"}]); //701241517
	}
	//StarTrack
	if ( number.match(/^MGXZ\d{8}[A-Z]{3}\d{5}$|^[A-Z]\d[A-Z]{2}\d{8}$|[A-Z]{4}\d{8}$|^\d[A-Z]{3}\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"startrack": "StarTrack"}]); //FYEZ00098398
	}
	//Swiss Post
	if ( number.match(/^L[A-Z]\d{9}CH$|^996\d{15}$/, '') ) {
		arr = jQuery.merge( arr,[{"swiss-post": "Swiss Post"}]); //LP425051106FR
	}
	//Szendex
	if ( number.match(/^009\d{15}$/, '') ) {
		arr = jQuery.merge( arr,[{"szendex": "Szendex"}]); //009000241336000001
	}
	//T Cat
	if ( number.match(/^909\d{9}$|^400\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"t-cat": "T Cat"}]); //400250146262
	}
	//TCI Express
	if ( number.match(/^94\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"tci-express": "TCI Express"}]); //949230903
	}
	//TCS express
	if ( number.match(/^65\d{8}$|^77\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"tcs-express": "TCS express"}]); //6577200821
	}
	//Teleport
	if ( number.match(/^TP\d{15}MY$/, '') ) {
		arr = jQuery.merge( arr,[{"teleport": "Teleport"}]); //TP175264856106336MY
	}
	//TForce Freight
	if ( number.match(/^6\d{8}$|^2\d{8}$|^0\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"tforce-freight": "TForce Freight"}]); //678608965
	}
	//Thailand Post
	if ( number.match(/^[A-Z]{2}\d{9}TH$/, '') ) {
		arr = jQuery.merge( arr,[{"thailand-post": "Thailand Post"}]); //RR311112669TH
	}
	//The Courier Guy
	if ( number.match(/^\w{5}$/, '') ) {
		arr = jQuery.merge( arr,[{"the-courier-guy": "The Courier Guy"}]); //PZBDW
	}
	//The Professional Couriers
	if ( number.match(/^[A-Za-z]{3}\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"the-professional-couriers": "The Professional Couriers"}]); //EDQ10862298
	}
	//TIKI
	if ( number.match(/^6600\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"tiki": "TIKI"}]); //660035821723
	}
	//Timelytitan
	if ( number.match(/^[A-Z]{2}\d{13}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"timelytitan": "Timelytitan"}]); //TT2134615365191CN
	}
	//Tipsa
	if ( number.match(/^03\d{20}$/, '') ) {
		arr = jQuery.merge( arr,[{"tip-sa": "Tipsa"}]); //0300030300030010848670
	}
	//TNT
	if ( number.match(/^20\d{7}$|^21\d{7}$|^22\d{7}$|^97\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"tnt": "TNT"}]); //720111883
	}
	//TNT Australia
	if ( number.match(/^[A-Z]{3}\d{9}$|^20\d{7}$|^36\d{7}$|^35\d{7}$|^37\d{7}$|^38\d{7}$|^18\d{7}$|^19\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"tnt-australia": "TNT Australia"}]); //ECU000020788
	}
	//TNT France
	if ( number.match(/^GE\d{9}WW/, '') ) {
		arr = jQuery.merge( arr,[{"tnt-france": "TNT France"}]); //GE950540524WW
	}
	//TNT Italy
	if ( number.match(/^[A-Z]{2}\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"tnt-italy": "TNT Italy"}]); //MY50607298
	}
	//TNT reference
	if ( number.match(/^GE\d{9}LT$|^11\d{7}$|^10\d{7}$|^96\d{7}$|^14\d{7}$|^15\d{7}$|^31\d{6}$|^30\d{7}$|^20\d{7}$|^21\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"tnt-reference": "TNT Reference"}]); //GE972056967LT
	}
	//TNT Sweden
	if ( number.match(/^95\d{7}$|^96\d{7}$|^97\d{7}$|^98\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"tnt-sweden": "TNT Sweden"}]); //962818633
	}
	//TNT UK
	if ( number.match(/^GE\d{9}GB$|^13\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"tnt-uk": "TNT UK"}]); //GE971195415GB
	}
	//TOLL
	if ( number.match(/^0009\d{16}$|^6569\d{9}$|^7865\d{9}$|^816\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"toll": "TOLL"}]); //00093275102553917621
	}
	//TOLL IPEC
	if ( number.match(/^686\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"toll-ipec": "TOLL IPEC"}]); //6867510008236
	}
	//TopYou
	if ( number.match(/^TYZRB\d{10}YQ$/, '') ) {
		arr = jQuery.merge( arr,[{"topyou": "TopYou"}]); //TYZRB0002876103YQ
	}
	//Trackon
	if ( number.match(/^500\d{9}$|^235\d{7}$|^142\d{7}$|^816\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"trackon": "Trackon"}]); //500194236212
	}
	//Turkey Post
	if ( number.match(/^[A-Z]{2}\d{9}TR$/, '') ) {
		arr = jQuery.merge( arr,[{"turkey-post": "Turkey Post"}]); //RC618111459DE
	}
	//UBI Smart Parcel
	if ( number.match(/^AUS\d{12}$|^400\d{13}$|^007\d{17}$|^33[A-Z]{3}\d{18}$/, '') ) {
		arr = jQuery.merge( arr,[{"ubi-smart-parcel": "UBI Smart Parcel"}]); //AUS493465701512
	}
	//UBX Express
	if ( number.match(/^59\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"ubx-express": "UBX Express"}]); //5901674728
	}
	//Udaan Express
	if ( number.match(/^98\d{4}$|^74\d{4}$|^75\d{4}$/, '') ) {
		arr = jQuery.merge( arr,[{"udaan-express": "Udaan Express"}]); //986386
	}
	//UK Mail
	if ( number.match(/^41\d{12}$/, '') ) {
		arr = jQuery.merge( arr,[{"ukmail": "UK Mail"}]); //41323860026336
	}
	//Ukraine EMS
	if ( number.match(/^EA\d{9}UA$/, '') ) {
		arr = jQuery.merge( arr,[{"ukraine-ems": "Ukraine EMS"}]); //EA060731285UA
	}
	//Ukrposhta
	if ( number.match(/^R[A-Z]\d{9}UA$|^CP\d{9}UA$/, '') ) {
		arr = jQuery.merge( arr,[{"ukrposhta": "Ukrposhta"}]); //RG067234002UA
	}
	//UPS Germany
	if ( number.match(/^1Z\w{9}\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"ups-germany": "UPS Germany"}]); //1Z5A69466801690441
	}
	//UPS Global
	if ( number.match(/^1Z\w{9}\d{7}$|^927\d{23}$/, '') ) {
		arr = jQuery.merge( arr,[{"ups-global": "UPS Global"}]); //1Z984RF96838631960
	}
	//UPS i-parcel
	if ( number.match(/^80\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"ups-i-parcel": "UPS i-parcel"}]); //802193621195252490
	}
	//UPS UK
	if ( number.match(/^1Z\w{9}\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"ups-uk": "UPS UK"}]); //1Z38Y46X6892809607
	}
	//UPS.se
	if ( number.match(/^1Z\w{9}\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"ups-se": "UPS.se"}]); //1Z6920AV0440685301
	}
	//Usky express
	if ( number.match(/^US{3}\d{10}YQ$/, '') ) {
		arr = jQuery.merge( arr,[{"usky-express": "Usky express"}]); //USKAA8001481609YQ
	}
	//Venipak
	if ( number.match(/^V\w{13}$/, '') ) {
		arr = jQuery.merge( arr,[{"venipak": "Venipak"}]); //V03164E1151984
	}
	//Vietnam Post
	if ( number.match(/^[A-Z]{2}\d{9}VN$/, '') ) {
		arr = jQuery.merge( arr,[{"vietnam-post": "Vietnam Post"}]); //EC136431858VN
	}
	//Wahana
	if ( number.match(/^\w{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"rides": "Wahana"}]); //BNLRNTDF
	}
	//wanbexpress
	if ( number.match(/^WN{3}\d{10}[A-Z]{2}$/, '') ) {
		arr = jQuery.merge( arr,[{"wanbexpress": "wanbexpress"}]); //WNBAA0163882238YQ
	}
	//Whistl
	if ( number.match(/^JD\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"whistl": "Whistl"}]); //JD0002219851355288
	}
	//Wiseloads
	if ( number.match(/^WSL\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"wiseloads": "Wiseloads"}]); //WSL005146858
	}
	//wnDirect
	if ( number.match(/^DI\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"wndirect": "wnDirect"}]); //DI039772535
	}
	//XDP Express
	if ( number.match(/^ZD{4}\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"xdp-uk": "XDP Express"}]); //ZDBGAU003049
	}	
	//XPO Logistics
	if ( number.match(/^41\d{7}$|^60\d{7}$|^53\d{7}$|^44\d{7}$|^13\d{7}$|^53\d{7}$|^37\d{7}$|^32\d{7}$|^88\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"xpo-logistics": "XPO Logistics"}]); //445181122
	}
	//XPOST
	if ( number.match(/^\d{8}[A-Z]{4}$/, '') ) {
		arr = jQuery.merge( arr,[{"xpost": "XPOST"}]); //8807-5430-MEWL
	}
	//Xpressbees
	if ( number.match(/^143\d{11}$|^126\d{10}$|^141\d{11}$|^133\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"xpressbees": "Xpressbees"}]); //14344921606402
	}
	//Yamato
	if ( number.match(/^76\d{10}$|^27\d{10}$|^47\d{10}$|^43\d{10}$|^28\d{10}$|^37\d{10}$/, '') ) {
		arr = jQuery.merge( arr,[{"yamato": "Yamato"}]); //276571216773
	}
	//Yanwen
	if ( number.match(/^UG\d{9}YP$/, '') ) {
		arr = jQuery.merge( arr,[{"yanwen": "Yanwen"}]); //UG336196744YP
	}
	//Yodel
	if ( number.match(/^JD\d{16}$|^JJD\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"yodel": "Yodel"}]); //JJD0002247492936157
	}
	//Yodel Direct
	if ( number.match(/^JD\d{16}$|^JJD\d{16}$/, '') ) {
		arr = jQuery.merge( arr,[{"yodel-direct": "Yodel Direct"}]); //JJD0002247492936157
	}	
	//YRC
	if ( number.match(/^760\d{7}$|^796\d{7}$|^604\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"yrc": "YRC"}]); //7963202917
	}
	//Yurtiçi Kargo
	if ( number.match(/^406\d{9}$|^905\d{9}$|^101\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"yurtici-kargo": "Yurtiçi Kargo"}]); //905086794211
	}
	//Zajil Express
	if ( number.match(/^Z\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"zajil-express": "Zajil Express"}]); //Z5811046
	}
	//Zeleris
	if ( number.match(/^\w{17}[A-Z]$|^\w{16}[A-Z]$/, '') ) {
		arr = jQuery.merge( arr,[{"zeleris": "Zeleris"}]); //16455390427274451S
	}
	//Zip
	if ( number.match(/^AS\d{9}$/, '') ) {
		arr = jQuery.merge( arr,[{"zip": "Zip"}]); //AS011822301
	}
	//Allied Express
	if ( number.match(/^NEW\d{6}$/, '') ) {
		arr = jQuery.merge( arr,[{"alliedexpress": "Allied Express"}]); //NEW626787
	}
	//R&L Carriers
	if ( number.match(/^26\d{7}$/, '') ) {
		arr = jQuery.merge( arr,[{"rl-carriers": "RL Carriers"}]); //269420805
	}
	//Estes
	if ( number.match(/^17\d{8}$/, '') ) {
		arr = jQuery.merge( arr,[{"estes": "Estes"}]); //1721273421
	}	

	
	//console.log(arr);
	var providers = autodetector_orders_params.provider_array;

	//console.log(providers);
	
	jQuery(".tracking_provider_dropdown").children().remove("optgroup[label='Matching Provider']");
	var selected_option = '';	
	var optgroup = jQuery("<optgroup label='Matching Provider'>");		
	var num = 0;
	jQuery.each( arr, function( key, value ) {			
		jQuery.each( value, function( provider_slug, provider_name ) {
			if ( providers[ provider_slug ] ) {
				if( num == 0 ){
					selected_option = provider_slug;
				}
				var op = "<option value='" + provider_slug + "'>" + provider_name + "</option>";				
				optgroup.append(op);
				num++;
			}
		});		
	});
	var link = null;
	if ( providers[ selected_option ] ) {
		link = providers[selected_option];
		link = link.replace( '%25number%25', number );					
		link = decodeURIComponent( link );

		jQuery( 'p.custom_tracking_link_field, p.custom_tracking_provider_field' ).hide();
	}
	
	if ( null != link ) {
		jQuery( 'p.preview_tracking_link a' ).attr( 'href', link );
		jQuery( 'p.preview_tracking_link' ).show();
	}

	jQuery(".tracking_provider_dropdown").prepend(optgroup);
	jQuery('.tracking_provider_dropdown').val(selected_option);
	//jQuery('.tracking_provider_dropdown').select2();
	jQuery('.tracking_provider_dropdown').select2({
		matcher: modelMatcher
	});
});