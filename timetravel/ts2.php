<?php
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=timesheet.pdf");
$Dept="IT";
$Name="Hacker, J. Random";
?>%PDF-1.1
%����
2 0 obj
<<
/Length 4443
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
[(DEPARTM)-14(E)1(NT:)-17(   <?=$Dept?>)]TJ
0 -1.1316 TD
-0.01 Tc
0.023 Tw
[(NAM)-16(E)-1(:)-19(    <?=$Name?>)]TJ
T*
-0.008 Tc
0.021 Tw
[(POSI)-14(TI)-14(ON #)-8(:)-17( .)]TJ
T*
[(EM)-14(PLOYEE #)-8(:)-17( .)]TJ
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
[(RUN I)-16(D)1(:)-19( AAAAA)]TJ
-4.7105 -1.1316 TD
-0.009 Tc
0.022 Tw
[(PAY BEGI)-15(N)2( DATE:)-18( BBBBB)]TJ
0.2895 -1.1316 TD
[(PAY END DATE #)-9(:)-18( CCCCC)]TJ
/F2 1 Tf
-61.3947 -3.9474 TD
-0.008 Tc
0.021 Tw
[(AUTOM)-14(ATI)-17(C)1(ALLY GENERATED TI)-17(M)-14(E)-2( S)-5(H)3(EET)]TJ
5.8684 -2.2105 TD
0.01 Tc
0.003 Tw
[(A)21(u)10(th)10(e)7(n)10(tic)7(a)7(tio)10(n)10( C)19(o)10(d)10(e)7(s:)]TJ
-2.1579 -1.7368 TD
-0.011 Tc
(AA: <?=date("D, j M Y")?>)Tj
0 -1.1053 TD
-0.009 Tc
(BB: <?=date("H:i:j T")?>)Tj
T*
(CC: <?=$REMOTE_ADDR?>)Tj
T*
-0.011 Tc
(DD:)Tj
-4.1579 -15.6842 TD
0 Tc
0.013 Tw
[(C)9(O)11(M)-6(M)-6(E)6(N)11(T)6(S:)-11( _______________________________)]TJ
0 12 -12 0 354.48 66 Tm
(________________________________)Tj
0 -1.68 TD
(________________________________)Tj
T*
(________________________________)Tj
T*
(________________________________)Tj
/F4 1 Tf
0 9.12 -9.12 0 116.16 275.52 Tm
-0.011 Tc
[(DATE)-2370(I)-17(N)-2657(O)-22(UT)-2660(I)-17(N)-2657(O)-22(U)0(T)-2660(I)-17(N)]TJ
0 10.08 -10.08 0 426.24 466.8 Tm
0.008 Tc
[(   PAY PERIOD TOTALS)]TJ
0 9.12 -9.12 0 116.16 512.88 Tm
-0.011 Tc
[(O)-22(U)0(T)-2660(I)-17(N)-2657(O)-22(UT)-2002(REG)-22(.)]TJ
12.7895 -1.1579 TD
0.011 Tc
[(HR)22(S)14(.)]TJ
4.5789 1.1579 TD
[(OT)20(.)]TJ
-0.2895 -1.1579 TD
[(HR)22(S)14(.)]TJ
3.6842 1.1579 TD
-0.01 Tc
[(TO)-21(TAL)]TJ
0.6053 -1.1579 TD
0.011 Tc
[(HR)22(S)14(.)]TJ
/F2 1 Tf
-71.2105 -37.0789 TD
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
23.3421 2.1842 TD
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
150.72 98.4 72.24 0.24 re
f
150.72 225.6 72.24 0.24 re
f
150.72 98.4 0.24 127.44 re
f
222.72 98.4 0.24 127.44 re
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
407.52 620.4 29.76 0.24 re
f
407.52 620.4 0.24 117.36 re
f
437.04 620.4 0.24 117.36 re
f
407.52 659.52 29.76 0.24 re
f
407.52 698.64 29.76 0.24 re
f
104.88 620.4 302.64 0.24 re
f
104.88 659.52 302.64 0.24 re
f
104.88 698.64 302.64 0.24 re
f
104.88 268.56 0.24 469.2 re
f
407.52 737.52 29.76 0.24 re
f
104.88 737.52 302.64 0.24 re
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
/CreationDate (D:191010422035655)
/Producer (\376\377\000A\000c\000r\000o\000b\000a\000t\000 \000D\000i\000s\000t\000i\000l\000l\000e\000r\000 \0003\000.\0000\0001\000 \000f\000o\000r\000 \000W\000i\000n\000d\000o\000w\000s)
/Creator (Wind/U Xprinter Version 3.1.0 \(linux\) \(Compile Date: Aug  4 1999 13:39:24\) \(XpDummyUser\))
/Title ()
>>
endobj
xref
0 13
0000000000 65535 f
0000006344 00000 n
0000000017 00000 n
0000004518 00000 n
0000004856 00000 n
0000004964 00000 n
0000005071 00000 n
0000004777 00000 n
0000006444 00000 n
0000004645 00000 n
0000005184 00000 n
0000006533 00000 n
0000006589 00000 n
trailer
<<
/Size 13
/Root 11 0 R
/Info 12 0 R
/ID [<7d7eef9bd9b91d3f9301b01aa6ab9418><7d7eef9bd9b91d3f9301b01aa6ab9418>]
>>
startxref
6960
%%EOF
