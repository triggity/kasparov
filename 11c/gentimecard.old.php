<?php
include "config.php";
include "database.php";
include "functions.php";
include "login.php";


MustLogIn();

//Make sure non admins are editing themselves
if ($userdata["IsAdmin"]=="N" || ""==$IdNum) { $IdNum=$CampusID; }

	$ThisCard=mysql_query("
		SELECT
			RunID AS Run,
			Stat1.Time AS Begin,
			Stat2.Time AS End,
			TimeCards_Data.Start AS StartHidden,
			TimeCards_Data.Stop AS StopHidden,
			Reg,
			PendReg,
			OT,
			PendOT,
			(Reg+PendReg+OT+PendOT) AS Total,
			Status,
			TimeCard,
			concat(Last,', ',First,' ',Middle) AS FullName,
			DATE_FORMAT(TimeCards_Periods.Start,'%c/%e/%Y') AS RunStart,
			DATE_FORMAT(TimeCards_Periods.End,'%c/%e/%Y') AS RunStop
		FROM	
			TimeCards_Data,
			Locations_Data AS Stat1,
			Locations_Data AS Stat2,
			TimeCards_Periods,
			People
		WHERE
			TimeCards_Data.CardID=$CardID AND
			TimeCards_Data.CampusID=$IdNum AND
			Stat1.RecordID=TimeCards_Data.Start AND
			Stat2.RecordID=TimeCards_Data.Stop AND
			TimeCards_Periods.PeriodID=TimeCards_Data.Period AND
			People.CampusID=$IdNum
		ORDER BY StartHidden DESC
		");

if (mysql_num_rows($ThisCard)!=1) { //problem!

	include "header.php";
	echo "<H2>Unable to load time card: $CardID</H2>";
	include "footer.php";
	exit();

}

$PinPoints=mysql_query("
	SELECT
		DATE_FORMAT(Time,'%c/%e'),
		FLOOR((HOUR(Time)*4+FLOOR(MINUTE(Time)/15))/TimeQuantum)*TimeQuantum/4.0 AS StartHour,
		FLOOR((HOUR(Time)*4+FLOOR(MINUTE(Time)/15))/TimeQuantum+1)*TimeQuantum/4.0 AS EndHour,
		YEAR(Time)*54+WEEK(Time),
		Time
	FROM
		Locations_Data,
		TimeCards_Locations,
		Locations
	WHERE
		Locations_Data.CampusID=$IdNum AND
		Locations_Data.RecordID>=".mysql_result($ThisCard,0,'StartHidden')." AND
		Locations_Data.RecordID<=".mysql_result($ThisCard,0,'StopHidden')." AND
		TimeCards_Locations.TimeCard=".mysql_result($ThisCard,0,'TimeCard')." AND
		Locations_Data.LocationID=TimeCards_Locations.Location AND
		Locations.ID=Locations_Data.LocationID
	ORDER BY Time
	");
//MakeTable($PinPoints,1,1,1,1,"");
$col=0;
$row=-1;
$TotalHours=0.0;
$ThisStart=-1;
$ThisEnd=-1;
for ($i=0;$i<mysql_num_rows($PinPoints);$i++) {
	if (mysql_result($PinPoints,$i,0)!=$CurrentDay || $col>7) {
		$CurrentDay=mysql_result($PinPoints,$i,0);

		if ($ThisEnd>=0) {
//			echo "$ThisStart::$ThisEnd ".(floor($ThisStart*4)%4)."::".(floor($ThisEnd*4)%4)."<BR>\n";
			$TableData[$row][$col]=floor($ThisStart).":".(((floor($ThisStart*60)%60)==0)?'00':(floor($ThisStart*60)%60));
			$TableData[$row][$col+1]=floor($ThisEnd).":".(((floor($ThisEnd*60)%60)==0)?'00':(floor($ThisEnd*60)%60));
		$TableData[$row][9]=$RowHours+($ThisEnd-$ThisStart);
		$TotalHours+=$TableData[$row][9];
		$TableData[$row][10]='0';
		$TableData[$row][11]=$TableData[$row][9];//$TotalHours;
		}
		$CurrentDay=mysql_result($PinPoints,$i,0);
		$row++;
		$TableData[$row][0]=$CurrentDay;
		$col=1;
		$ThisStart=mysql_result($PinPoints,$i,1);
		$ThisEnd=mysql_result($PinPoints,$i,2);
		$RowHours=0.0;
	} else if (mysql_result($PinPoints,$i,1)>$ThisEnd) {
			$TableData[$row][$col]=floor($ThisStart).":".(((floor($ThisStart*60)%60)==0)?'00':(floor($ThisStart*60)%60));
			$TableData[$row][$col+1]=floor($ThisEnd).":".(((floor($ThisEnd*60)%60)==0)?'00':(floor($ThisEnd*60)%60));
		$RowHours+=($ThisEnd-$ThisStart);
		$ThisStart=mysql_result($PinPoints,$i,1);
		$ThisEnd=mysql_result($PinPoints,$i,2);
		$col+=2;
	} else {
		$ThisEnd=mysql_result($PinPoints,$i,2);
	}		
}			
//Last check for data....
		if ($ThisEnd>=0) {
//			echo "$ThisStart::$ThisEnd ".(floor($ThisStart*4)%4)."::".(floor($ThisEnd*4)%4)."<BR>\n";
			$TableData[$row][$col]=floor($ThisStart).":".(((floor($ThisStart*60)%60)==0)?'00':(floor($ThisStart*60)%60));
			$TableData[$row][$col+1]=floor($ThisEnd).":".(((floor($ThisEnd*60)%60)==0)?'00':(floor($ThisEnd*60)%60));
		$TableData[$row][9]=$RowHours+($ThisEnd-$ThisStart);
		$TotalHours+=$TableData[$row][9];
		$TableData[$row][10]='0';
		$TableData[$row][11]=$TableData[$row][9];//$TotalHours;
		}

$TableData[16][9]=mysql_result($ThisCard,0,'Reg')+0.0;
$TableData[16][10]=mysql_result($ThisCard,0,'OT')+0.0;
$TableData[16][11]=$TableData[16][9]+$TableData[16][10];
$TableData[17][9]=mysql_result($ThisCard,0,'PendReg')+0.0;
$TableData[17][10]=mysql_result($ThisCard,0,'PendOT')+0.0;
$TableData[17][11]=$TableData[17][9]+$TableData[17][10];
$TableData[18][9]=$TableData[16][9]+$TableData[17][9];
$TableData[18][10]=$TableData[16][10]+$TableData[17][10];
$TableData[18][11]=$TableData[16][11]+$TableData[17][11];
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=timesheet.pdf");
?>%PDF-1.1
%‚„œ”
2 0 obj
<<
/Length 6017
>>
stream
BT
/F2 1 Tf
0 14.16 -14.16 0 48.96 378 Tm
0 0 0 rg
BX /GS1 gs EX
0 Tc
0 Tw
( )Tj
/F4 1 Tf
0 12 -12 0 48.96 381.6 Tm
-0.003 Tc
0.013 Tw
[(S)-7(ANTA CLARA UNI)6(VERS)-7(I)6(T)4(Y)]TJ
/F6 1 Tf
0 9.12 -9.12 0 69.12 56.64 Tm
-0.008 Tc
0.021 Tw
[(DEPARTMENT:)-17( IT)]TJ
0 -1.1316 TD
-0.01 Tc
0.023 Tw
[(NAME:)-19( <?=mysql_result($ThisCard,0,'FullName')?>)]TJ
T*
-0.008 Tc
0.021 Tw
[(POSI)-14(TI)-14(ON #)-8(:)-17( .)]TJ
T*
[(EM)-14(PLOYEE #)-8(:)-17( <?=$IdNum?>)]TJ
14.4737 2.2632 TD
0.013 Tc
0 Tw
[(                         P)19(A)22(Y)19( G)24(R)22(O)24(U)24(P)19(: .)]TJ
0 -1.1316 TD
0.012 Tc
0.001 Tw
[(     J)12(O)23(B)21( T)18(I)6(T)18(L)18(E)21(: .)]TJ
T*
-0.008 Tc
0.021 Tw
[(REC#)-8(:)-17(             FTE:)-17(               EM)-14(P.)-21( TYPE:)]TJ
/F4 1 Tf
0 10.08 -10.08 0 80.4 409.68 Tm
0.02 Tw
[(ST)-8(UDE)-8(NT)-8( T)-8(I)0(M)-16(E)-8( SH)-16(E)-8(E)-8(T)]TJ
/F6 1 Tf
0 9.12 -9.12 0 69.12 667.44 Tm
-0.01 Tc
0.023 Tw
[(RUN I)-16(D)1(:)-19( <?=mysql_result($ThisCard,0,'Run')?>)]TJ
-4.7105 -1.1316 TD
-0.009 Tc
0.022 Tw
[(PAY BEGI)-15(N)2( DATE:)-18( <?=mysql_result($ThisCard,0,'RunStart')?>)]TJ
0.2895 -1.1316 TD
[(   PAY END DATE)-9(:)-18( <?=mysql_result($ThisCard,0,'RunStop')?>)]TJ
/F2 1 Tf
-61.3947 -3.9474 TD
-0.008 Tc
0.021 Tw
[(AUTOM)-14(ATI)-17(C)1(ALLY GENERATED TI)-17(M)-14(E)-2( S)-5(H)3(EET)]TJ
6.1842 -2.2105 TD
0.011 Tc
0.002 Tw
[(A)22(u)11(th)11(e)8(n)11(tic)8(a)8(tio)11(n)11( D)22(a)8(ta)8(:)]TJ
-4.8684 -2.0263 TD
0.009 Tc
[(CardID:)-1239(<?=$CardID?>)]TJ
0 9.12 -9.12 0 164.4 165.2 Tm
0 Tc
0 Tw
(Status:       <?=mysql_result($ThisCard,0,'Status')?>)Tj
0 9.12 -9.12 0 175.2 79.2 Tm
0.002 Tc
[(StartRec:   <?=mysql_result($ThisCard,0,'StartHidden')?>)]TJ
0 9.12 -9.12 0 175.2 165.2 Tm
0.002 Tc
[(StopRec:   <?=mysql_result($ThisCard,0,'StopHidden')?>)]TJ
0 -1.1053 TD
0.011 Tc
0 9.12 -9.12 0 186.0 79.2 Tm
0.002 Tc
[()]TJ
[(T)17(o)11(ta)8(l:)-2106(.)]TJ
T*
0.001 Tc
[(Sus)-5(p)1(ect)-10(:)-1116(.)-4696(D)12(oubl)-10(ed:)-537(.)]TJ
T*
0.002 Tc
[(O)13(K)13(:)-2825(.)-4695(E)8(xpect)-9(ed:)-246(.)]TJ
T*
0 9.12 -9.12 0 218.2 79.2 Tm
-0.011 Tc
0.024 Tw
[(Now:)-22(  <?=date("m/d H:i:j")?>)]TJ
T*
0 9.12 -9.12 0 218.2 165.2 Tm
-0.011 Tc
0.024 Tw
[(IP:  <?=$REMOTE_ADDR?>)]TJ
T*
0 9.12 -9.12 0 218.2 79.2 Tm
-0.011 Tc
0.024 Tw
[(N)]TJ
-1.7632 -12 TD
0 Tc
0.013 Tw
[(C)9(O)11(M)-6(M)-6(E)6(N)11(T)6(S:)-11( _______________________________)]TJ
0 12 -12 0 344.4 66 Tm
(________________________________)Tj
0 -1.68 TD
(________________________________)Tj
T*
(________________________________)Tj
T*
(________________________________)Tj
/F4 1 Tf
0 9.12 -9.12 0 116.16 275.52 Tm
-0.01 Tc
(DATE)Tj
<?
for ($col=0;$col<12;$col++) {
	echo '0 10.08 -10.08 0 145.92 '.(271.2+$col*39.12).' Tm';
	echo '0 Tc';
	echo '0 Tw';
	for ($row=0;$row<20;$row++) {
		echo "(".$TableData[$row][$col].")Tj";
		echo '0 -1.6905 TD';
	}
	echo "()Tj\n";
}
?>0 9.12 -9.12 0 116.16 322.08 Tm
0.006 Tc
(IN)Tj
0 9.12 -9.12 0 116.16 356.4 Tm
0.011 Tc
[(OU)22(T)]TJ
0 9.12 -9.12 0 116.16 400.32 Tm
0.006 Tc
(IN)Tj
0 10.08 -10.08 0 145.92 388.56 Tm
0 Tc
()Tj
0 -30.5238 TD
( )Tj
0 9.12 -9.12 0 116.16 434.64 Tm
0.011 Tc
[(OU)22(T)]TJ
0 10.08 -10.08 0 145.92 427.68 Tm
0 Tc
( )Tj
1.6905 -27.1429 TD
-0.008 Tc
(    )Tj
0 9.12 -9.12 0 116.16 478.56 Tm
0.006 Tc
(IN)Tj
0 10.08 -10.08 0 145.92 466.8 Tm
0 Tc
( )Tj
T*
[-10000(Sub Total)]TJ
0 -1.6905 TD
0.008 Tc
[-10000(Pending)]TJ
0 -2.3571 TD
[-10000(PAY)]TJ
0 9.12 -9.12 0 116.16 512.88 Tm
0.011 Tc
[(OU)22(T)]TJ
0 9.12 -9.12 0 116.16 556.8 Tm
0.006 Tc
(IN)Tj
0 9.12 -9.12 0 116.16 591.12 Tm
0.011 Tc
[(OU)22(T)]TJ
0 9.12 -9.12 0 116.16 629.04 Tm
-0.01 Tc
[(REG)-21(.)]TJ
0.0526 -1.1579 TD
0.011 Tc
[(HR)22(S)14(.)]TJ
0 9.12 -9.12 0 116.16 671.28 Tm
0.011 Tc
[(OT)20(.)]TJ
-0.2895 -1.1579 TD
[(HR)22(S)14(.)]TJ
0 9.12 -9.12 0 116.16 702.24 Tm
-0.01 Tc
[(TO)-21(TAL)]TJ
0.6053 -1.1579 TD
0.011 Tc
[(HR)22(S)14(.)]TJ
/F2 1 Tf
0 9.12 -9.12 0 502.08 58.32 Tm
-0.008 Tc
0.021 Tw
[(TI)-17(M)-14(E)-2( SHEETS M)-14(U)3(ST BE RECEI)-17(V)3(ED BY NOON ON)]TJ
0 -1.1053 TD
-0.007 Tc
0.02 Tw
[(THE DESI)-16(GNATED PAYROLL CUTOFF DATE TO)]TJ
T*
-0.008 Tc
0.021 Tw
[(GUARANTEE TI)-17(M)-14(ELY PAYM)-14(ENT.)]TJ
/F4 1 Tf
23.3421 2.1579 TD
-0.01 Tc
0.023 Tw
[(    I)-16( CERTI)-16(F)-4(Y TH)-21(AT TH)-21(I)-16(S)-7( REPO)-21(RT I)-16(S)-7( ACCURATE AND TRUE)]TJ
0 -5.4474 TD
0 Tc
0.013 Tw
[(___________________________________________                   ___________________________________________)]TJ
0 -1.8158 TD
-0.01 Tc
0.023 Tw
[(EM)-13(PLO)-21(Y)1(EE S)-7(I)-16(G)-21(NATURE              DATE                                S)-7(U)1(PERVI)-16(S)-7(O)-21(R/)-21(M)-13(ANAG)-21(ER S)-7(I)-16(G)-21(NATURE    EXT.)]TJ
ET
1 i 
150.72 76.56 72.24 0.24 re
f
150.72 248.16 72.24 0.24 re
f
150.72 76.56 0.24 171.84 re
f
222.72 76.56 0.24 171.84 re
f
104.88 268.56 302.64 0.24 re
f
133.68 268.56 0.24 469.2 re
f
150.72 268.56 0.24 469.2 re
f
167.76 268.56 0.24 469.2 re
f
184.8 268.56 0.24 469.2 re
f
202.08 268.56 0.24 469.2 re
f
219.12 268.56 0.24 469.2 re
f
236.16 268.56 0.24 469.2 re
f
253.44 268.56 0.24 469.2 re
f
270.48 268.56 0.24 469.2 re
f
287.52 268.56 0.24 469.2 re
f
304.56 268.56 0.24 469.2 re
f
321.84 268.56 0.24 469.2 re
f
338.88 268.56 0.24 469.2 re
f
355.92 268.56 0.24 469.2 re
f
373.2 268.56 0.24 469.2 re
f
390.24 268.56 0.24 469.2 re
f
407.28 268.56 0.24 469.2 re
f
104.88 307.68 302.64 0.24 re
f
104.88 346.8 302.64 0.24 re
f
104.88 385.92 302.64 0.24 re
f
104.88 425.04 302.64 0.24 re
f
104.88 464.16 302.64 0.24 re
f
104.88 503.28 302.64 0.24 re
f
104.88 542.4 302.64 0.24 re
f
104.88 581.28 302.64 0.24 re
f
441.84 620.4 32.88 0.24 re
f
424.32 620.4 0.24 117.36 re
f
441.6 620.4 0.24 117.36 re
f
441.84 620.4 0.24 117.36 re
f
474.48 620.4 0.24 117.36 re
f
441.84 659.52 32.88 0.24 re
f
441.84 698.64 32.88 0.24 re
f
104.88 620.4 336.96 0.24 re
f
104.88 659.52 336.96 0.24 re
f
104.88 698.64 336.96 0.24 re
f
104.88 268.56 0.24 469.2 re
f
441.84 737.52 32.88 0.24 re
f
104.88 737.52 336.96 0.24 re
f
endstream
endobj
3 0 obj
<<
/ProcSet [/PDF /Text ]
/Font <<
/F2 4 0 R
/F4 5 0 R
/F6 6 0 R
>>
/ExtGState <<
/GS1 7 0 R
>>
>>
endobj
9 0 obj
<<
/Type /Halftone
/HalftoneType 1
/HalftoneName (Default)
/Frequency 60
/Angle 45
/SpotFunction /Round
>>
endobj
7 0 obj
<<
/Type /ExtGState
/SA false
/OP false
/HT /Default
>>
endobj
4 0 obj
<<
/Type /Font
/Subtype /Type1
/Name /F2
/Encoding 10 0 R
/BaseFont /Times-Roman
>>
endobj
5 0 obj
<<
/Type /Font
/Subtype /Type1
/Name /F4
/Encoding 10 0 R
/BaseFont /Times-Bold
>>
endobj
6 0 obj
<<
/Type /Font
/Subtype /Type1
/Name /F6
/Encoding 10 0 R
/BaseFont /Times-BoldItalic
>>
endobj
10 0 obj
<<
/Type /Encoding
/Differences [ 45/minus 128/euro 130/quotesinglbase/florin/quotedblbase/ellipsis/dagger/daggerdbl
/circumflex/perthousand/Scaron/guilsinglleft/OE 142/zcaron 144/dotlessi/quoteleft
/quoteright/quotedblleft/quotedblright/bullet/endash/emdash/tilde/trademark
/scaron/guilsinglright/oe/hungarumlaut/zcaron/Ydieresis/space 164/currency
 166/brokenbar 168/dieresis/copyright/ordfeminine 172/logicalnot/hyphen/registered/macron
/degree/plusminus/twosuperior/threesuperior/acute/mu 183/periodcentered/cedilla
/onesuperior/ordmasculine 188/onequarter/onehalf/threequarters 192/Agrave/Aacute/Acircumflex
/Atilde/Adieresis/Aring/AE/Ccedilla/Egrave/Eacute/Ecircumflex
/Edieresis/Igrave/Iacute/Icircumflex/Idieresis/Eth/Ntilde/Ograve
/Oacute/Ocircumflex/Otilde/Odieresis/multiply/Oslash/Ugrave/Uacute
/Ucircumflex/Udieresis/Yacute/Thorn/germandbls/agrave/aacute/acircumflex
/atilde/adieresis/aring/ae/ccedilla/egrave/eacute/ecircumflex
/edieresis/igrave/iacute/icircumflex/idieresis/eth/ntilde/ograve
/oacute/ocircumflex/otilde/odieresis/divide/oslash/ugrave/uacute
/ucircumflex/udieresis/yacute/thorn/ydieresis
]
>>
endobj
1 0 obj
<<
/Type /Page
/Parent 8 0 R
/Resources 3 0 R
/Contents 2 0 R
/Rotate 90
>>
endobj
8 0 obj
<<
/Type /Pages
/Kids [1 0 R]
/Count 1
/MediaBox [0 0 612 792]
>>
endobj
11 0 obj
<<
/Type /Catalog
/Pages 8 0 R
>>
endobj
12 0 obj
<<
/CreationDate (D:191010529091053)
/Producer (\376\377\000A\000c\000r\000o\000b\000a\000t\000 \000D\000i\000s\000t\000i\000l\000l\000e\000r\000 \0003\000.\0000\0001\000 \000f\000o\000r\000 \000W\000i\000n\000d\000o\000w\000s)
/Creator (Wind/U Xprinter Version 3.1.0 \(linux\) \(Compile Date: Aug  4 1999 13:39:24\) \(XpDummyUser\))
/Title ()
>>
endobj
xref
0 13
0000000000 65535 f
0000007918 00000 n
0000000017 00000 n
0000006092 00000 n
0000006430 00000 n
0000006538 00000 n
0000006645 00000 n
0000006351 00000 n
0000008018 00000 n
0000006219 00000 n
0000006758 00000 n
0000008107 00000 n
0000008163 00000 n
trailer
<<
/Size 13
/Root 11 0 R
/Info 12 0 R
/ID [<a3e90c093e8bb29dc97733498b456c27><a3e90c093e8bb29dc97733498b456c27>]
>>
startxref
8534
%%EOF
