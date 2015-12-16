#! /bin/bash
clear

HARGA_PAKET_SIMPATI=20000
HARGA_PAKET_XL=66000
HARGA_PAKET_INDOSAT=10000
NEXT_UPDATE_SIMPATI=$(<paketHabisSimpati.txt)
NEXT_UPDATE_XL=$(<paketHabisXL.txt)
NOW=$(date +%Y%m%d)
TUKANGPULSA=081381171337
TUKANGKETIK=08992112203

TELKOMSEL=(081295882084 082112592932 081213374483 081319468847 081295741478 081212232674 081212232835 081212232617 081212232638)
XL=(087780867200 087886347632 087883072681 087886340681)
INDOSAT=(081513779454 085710250748 085710250739)

echo "Resarting openvox..."
echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('${TELKOMSEL[0]}', 'system restart c3rmat', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('${TELKOMSEL[5]}', 'system restart c3rmat', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('${XL[0]}', 'system restart c3rmat', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('${INDOSAT[0]}', 'system restart c3rmat', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
sleep 3m

echo $(rm -rf /home/cermati/.ssh/known_hosts)
echo "Checking Pulsa Telkomsel1..."
# telkomsel1_Pulsa="1:Failed"
telkomsel1_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.2 -p12345 "asterisk -rx 'gsm send ussd 1 *888#'")
cekString=${telkomsel1_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel1_Pulsa=${telkomsel1_Pulsa:62:6}
	telkomsel1_Pulsa=${telkomsel1_Pulsa//[.Aktif]/}
	telkomsel1_Pulsa=$((telkomsel1_Pulsa + 0))
	telkomselPulsaArray[0]=$telkomsel1_Pulsa
	failCheckingTelkomsel[0]="success"
else
	failCheckingTelkomsel[0]="fail"
fi

echo "Checking Pulsa Telkomsel2..."
# telkomsel2_Pulsa="1:Failed"
telkomsel2_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.2 -p12345 "asterisk -rx 'gsm send ussd 2 *888#'")
cekString=${telkomsel2_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel2_Pulsa=${telkomsel2_Pulsa:62:6}
	telkomsel2_Pulsa=${telkomsel2_Pulsa//[.Aktif]/}
	telkomsel2_Pulsa=$((telkomsel2_Pulsa + 0))
	telkomselPulsaArray[1]=$telkomsel2_Pulsa
	failCheckingTelkomsel[1]="success"
else
	failCheckingTelkomsel[1]="fail"
fi

echo "Checking Pulsa Telkomsel3..."
# telkomsel3_Pulsa="1:Recive USSD on span 1,responses:1,code:0 Text:Sisa pulsa Rp.0.Aktif sd 25/11/2016. Mau MP3 RizkiRidho D2 Academy? Download MP3nya 1.YA 2.Info Kartu"
telkomsel3_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.2 -p12345 "asterisk -rx 'gsm send ussd 3 *888#'")
cekString=${telkomsel3_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel3_Pulsa=${telkomsel3_Pulsa:62:6}
	telkomsel3_Pulsa=${telkomsel3_Pulsa//[.Aktif]/}
	telkomsel3_Pulsa=$((telkomsel3_Pulsa + 0))
	telkomselPulsaArray[2]=$telkomsel3_Pulsa
	failCheckingTelkomsel[2]="success"
else
	failCheckingTelkomsel[2]="fail"
fi

echo "Checking Pulsa Telkomsel4..."
# telkomsel4_Pulsa="1:Recive USSD on span 1,responses:1,code:0 Text:Sisa pulsa Rp.23041.Aktif sd 25/11/2016. Mau MP3 RizkiRidho D2 Academy? Download MP3nya 1.YA 2.Info Kartu"
telkomsel4_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.2 -p12345 "asterisk -rx 'gsm send ussd 4 *888#'")
cekString=${telkomsel4_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel4_Pulsa=${telkomsel4_Pulsa:62:6}
	telkomsel4_Pulsa=${telkomsel4_Pulsa//[.Aktif]/}
	telkomsel4_Pulsa=$((telkomsel4_Pulsa + 0))
	telkomselPulsaArray[3]=$telkomsel4_Pulsa
	failCheckingTelkomsel[3]="success"
else
	failCheckingTelkomsel[3]="fail"
fi

echo "Checking Pulsa Telkomsel5..."
# telkomsel5_Pulsa="1:Recive USSD on span 1,responses:1,code:0 Text:Sisa pulsa Rp.23041.Aktif sd 25/11/2016. Mau MP3 RizkiRidho D2 Academy? Download MP3nya 1.YA 2.Info Kartu"
telkomsel5_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.3 -p12345 "asterisk -rx 'gsm send ussd 1 *888#'")
cekString=${telkomsel5_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel5_Pulsa=${telkomsel5_Pulsa:62:6}
	telkomsel5_Pulsa=${telkomsel5_Pulsa//[.Aktif]/}
	telkomsel5_Pulsa=$((telkomsel5_Pulsa + 0))
	telkomselPulsaArray[4]=$telkomsel5_Pulsa
	failCheckingTelkomsel[4]="success"
else
	failCheckingTelkomsel[4]="fail"
fi

echo "Checking Pulsa Telkomsel6..."
# telkomsel5_Pulsa="1:Recive USSD on span 1,responses:1,code:0 Text:Sisa pulsa Rp.23041.Aktif sd 25/11/2016. Mau MP3 RizkiRidho D2 Academy? Download MP3nya 1.YA 2.Info Kartu"
telkomsel6_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.3 -p12345 "asterisk -rx 'gsm send ussd 2 *888#'")
cekString=${telkomsel6_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel6_Pulsa=${telkomsel6_Pulsa:62:6}
	telkomsel6_Pulsa=${telkomsel6_Pulsa//[.Aktif]/}
	telkomsel6_Pulsa=$((telkomsel6_Pulsa + 0))
	telkomselPulsaArray[5]=$telkomsel6_Pulsa
	failCheckingTelkomsel[5]="success"
else
	failCheckingTelkomsel[5]="fail"
fi

echo "Checking Pulsa Telkomsel7..."
# telkomsel5_Pulsa="1:Recive USSD on span 1,responses:1,code:0 Text:Sisa pulsa Rp.23041.Aktif sd 25/11/2016. Mau MP3 RizkiRidho D2 Academy? Download MP3nya 1.YA 2.Info Kartu"
telkomsel7_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.3 -p12345 "asterisk -rx 'gsm send ussd 3 *888#'")
cekString=${telkomsel7_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel7_Pulsa=${telkomsel7_Pulsa:62:6}
	telkomsel7_Pulsa=${telkomsel7_Pulsa//[.Aktif]/}
	telkomsel7_Pulsa=$((telkomsel7_Pulsa + 0))
	telkomselPulsaArray[6]=$telkomsel7_Pulsa
	failCheckingTelkomsel[6]="success"
else
	failCheckingTelkomsel[6]="fail"
fi

echo "Checking Pulsa Telkomsel8..."
# telkomsel5_Pulsa="1:Recive USSD on span 1,responses:1,code:0 Text:Sisa pulsa Rp.23041.Aktif sd 25/11/2016. Mau MP3 RizkiRidho D2 Academy? Download MP3nya 1.YA 2.Info Kartu"
telkomsel8_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.3 -p12345 "asterisk -rx 'gsm send ussd 4 *888#'")
cekString=${telkomsel8_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel8_Pulsa=${telkomsel8_Pulsa:62:6}
	telkomsel8_Pulsa=${telkomsel8_Pulsa//[.Aktif]/}
	telkomsel8_Pulsa=$((telkomsel8_Pulsa + 0))
	telkomselPulsaArray[7]=$telkomsel8_Pulsa
	failCheckingTelkomsel[7]="success"
else
	failCheckingTelkomsel[7]="fail"
fi

echo "Checking Pulsa Telkomsel9..."
# telkomsel5_Pulsa="1:Recive USSD on span 1,responses:1,code:0 Text:Sisa pulsa Rp.23041.Aktif sd 25/11/2016. Mau MP3 RizkiRidho D2 Academy? Download MP3nya 1.YA 2.Info Kartu"
telkomsel9_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.4 -p12345 "asterisk -rx 'gsm send ussd 1 *888#'")
cekString=${telkomsel9_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	telkomsel9_Pulsa=${telkomsel9_Pulsa:62:6}
	telkomsel9_Pulsa=${telkomsel9_Pulsa//[.Aktif]/}
	telkomsel9_Pulsa=$((telkomsel9_Pulsa + 0))
	telkomselPulsaArray[8]=$telkomsel9_Pulsa
	failCheckingTelkomsel[8]="success"
else
	failCheckingTelkomsel[8]="fail"
fi

echo "Checking Pulsa XL..."
# XL_Pulsa="1:Recive USSD on span 4,responses:1,code:15 Text:Pulsa 80000 s.d 23Jan16. Pilih 1 utk GRATIS telp 15mnt + RBT Lucu 1 Mau 2 Plh Tarif 3 Inet/Cek Kuota 4 Nelp&SMS 5 Pkt Seru 6 mPulsa 7 I.L.M/MyInfo"
XL_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.5 -p12345 "asterisk -rx 'gsm send ussd 1 *123#'")
cekString=${XL_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	XL_Pulsa=${XL_Pulsa:55:6}
	XL_Pulsa=${XL_Pulsa//[ s.d ]/}
	XL_Pulsa=$((XL_Pulsa + 0))
	XLPulsaArray[0]=$XL_Pulsa
	failCheckingXL[0]="success"
else
	failCheckingXL[0]="fail"
fi

echo "Checking Pulsa XL2..."
# XL2_Pulsa="1:Recive USSD on span 4,responses:1,code:15 Text:Pulsa 22040 s.d 23Jan16. Pilih 1 utk GRATIS telp 15mnt + RBT Lucu 1 Mau 2 Plh Tarif 3 Inet/Cek Kuota 4 Nelp&SMS 5 Pkt Seru 6 mPulsa 7 I.L.M/MyInfo"
XL2_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.5 -p12345 "asterisk -rx 'gsm send ussd 2 *123#'")
cekString=${XL2_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	XL2_Pulsa=${XL2_Pulsa:55:6}
	XL2_Pulsa=${XL2_Pulsa//[ s.d ]/}
	XL2_Pulsa=$((XL2_Pulsa + 0))
	XLPulsaArray[1]=$XL2_Pulsa
	failCheckingXL[1]="success"
else
	failCheckingXL[1]="fail"
fi

echo "Checking Pulsa XL3..."
# XL3_Pulsa="1:Recive USSD on span 4,responses:1,code:15 Text:Pulsa 22040 s.d 23Jan16. Pilih 1 utk GRATIS telp 15mnt + RBT Lucu 1 Mau 2 Plh Tarif 3 Inet/Cek Kuota 4 Nelp&SMS 5 Pkt Seru 6 mPulsa 7 I.L.M/MyInfo"
XL3_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.5 -p12345 "asterisk -rx 'gsm send ussd 3 *123#'")
cekString=${XL3_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	XL3_Pulsa=${XL3_Pulsa:55:6}
	XL3_Pulsa=${XL3_Pulsa//[ s.d ]/}
	XL3_Pulsa=$((XL3_Pulsa + 0))
	XLPulsaArray[2]=$XL3_Pulsa
	failCheckingXL[2]="success"
else
	failCheckingXL[2]="fail"
fi

echo "Checking Pulsa XL4..."
# XL4_Pulsa="1:Failed"
XL4_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.5 -p12345 "asterisk -rx 'gsm send ussd 4 *123#'")
cekString=${XL4_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	XL4_Pulsa=${XL4_Pulsa:55:6}
	XL4_Pulsa=${XL4_Pulsa//[ s.d ]/}
	XL4_Pulsa=$((XL4_Pulsa + 0))
	XLPulsaArray[3]=$XL4_Pulsa
	failCheckingXL[3]="success"
else
	failCheckingXL[3]="fail"
fi

echo "Checking Pulsa Indosat..."
# indosat_Pulsa="1:Recive USSD on span 3,responses:2,code:15 Text:PulsaUTAMA Rp.58600. Aktif 27.02.16, Tenggang 28.03.16.Dptkn 10GB di 4G,konten&Bns 10rb mnt. cek: *123*46#"
indosat_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.4 -p12345 "asterisk -rx 'gsm send ussd 2 *123#'")
cekString=${indosat_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	indosat_Pulsa=${indosat_Pulsa:57:6}
	indosat_Pulsa=${indosat_Pulsa//[. Aktif]/}
	indosat_Pulsa=$((indosat_Pulsa + 0))
	indosatPulsaArray[0]=$indosat_Pulsa
	failCheckingIndosat[0]="success"
else
	failCheckingIndosat[0]="fail"
fi

echo "Checking Pulsa Indosat2..."
# indosat2_Pulsa="1:Recive USSD on span 3,responses:2,code:15 Text:PulsaUTAMA Rp.58600. Aktif 27.02.16, Tenggang 28.03.16.Dptkn 10GB di 4G,konten&Bns 10rb mnt. cek: *123*46#"
indosat2_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.4 -p12345 "asterisk -rx 'gsm send ussd 3 *123#'")
cekString=${indosat2_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	indosat2_Pulsa=${indosat2_Pulsa:57:6}
	indosat2_Pulsa=${indosat2_Pulsa//[. Aktif]/}
	indosat2_Pulsa=$((indosat2_Pulsa + 0))
	indosatPulsaArray[1]=$indosat2_Pulsa
	failCheckingIndosat[1]="success"
else
	failCheckingIndosat[1]="fail"
fi

echo "Checking Pulsa Indosat3..."
# indosat3_Pulsa="1:Recive USSD on span 3,responses:2,code:15 Text:PulsaUTAMA Rp.58600. Aktif 27.02.16, Tenggang 28.03.16.Dptkn 10GB di 4G,konten&Bns 10rb mnt. cek: *123*46#"
indosat3_Pulsa=$(sshpass -padmin ssh -o StrictHostKeyChecking=no admin@3.3.3.4 -p12345 "asterisk -rx 'gsm send ussd 4 *123#'")
cekString=${indosat3_Pulsa:2:6}
if [ "$cekString" = "Recive" ]; then
	indosat3_Pulsa=${indosat3_Pulsa:57:6}
	indosat3_Pulsa=${indosat3_Pulsa//[. Aktif]/}
	indosat3_Pulsa=$((indosat3_Pulsa + 0))
	indosatPulsaArray[2]=$indosat3_Pulsa
	failCheckingIndosat[2]="success"
else
	failCheckingIndosat[2]="fail"
fi

numSimpati=0
#alert Pulsa Simpati
for i in "${failCheckingTelkomsel[@]}"
do
	if [ "${failCheckingTelkomsel[$numSimpati]}" = "success" ]; then
		if [[ ${telkomselPulsaArray[$numSimpati]} -lt $HARGA_PAKET_SIMPATI ]]; then
			echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('$TUKANGPULSA', 'Pika.. Pika.. Minta pulsa chu~~ Simpati 25ribu nomor : ${TELKOMSEL[$numSimpati]}.. Pulsanya tinggal ${telkomselPulsaArray[$numSimpati]}', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
		fi
	else
		echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('$TUKANGKETIK', '${TELKOMSEL[$numSimpati]} gagal cek pulsa', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
	fi
	numSimpati=$((numSimpati + 1))
done

#alert Paket Habis Simpati
if [[ $NOW -ge $NEXT_UPDATE_SIMPATI ]]; then
	newDate=$(date -d "5 days" +%Y%m%d)
	echo "$newDate">paketHabisSimpati.txt
	echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ($TUKANGKETIK, ${TELKOMSEL[$numSimpati]} perpanjang paket... coba cek..!!!, BashAdmin);"| mysql -h1.1.1.107 -uroot -pc3rmat sms

	echo "Resarting openvox..."
	# echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('${TELKOMSEL[0]}', 'system restart c3rmat', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
	# echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('${TELKOMSEL[5]}', 'system restart c3rmat', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
	sleep 3m

	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.2 -p12345 "asterisk -rx 'gsm send sms 1 8999 TM JUMBO'")
	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.2 -p12345 "asterisk -rx 'gsm send sms 2 8999 TM JUMBO'")
	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.2 -p12345 "asterisk -rx 'gsm send sms 3 8999 TM JUMBO'")
	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.2 -p12345 "asterisk -rx 'gsm send sms 4 8999 TM JUMBO'")
	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.3 -p12345 "asterisk -rx 'gsm send sms 1 8999 TM JUMBO'")
fi

numXL=0
#alert Pulsa XL
for i in "${failCheckingXL[@]}"
do
	if [ "${failCheckingXL[$numXL]}" = "success" ]; then
		if [[ ${XLPulsaArray[$numXL]} -lt $HARGA_PAKET_XL ]]; then
			echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('$TUKANGPULSA', 'Pika.. Pika.. Minta pulsa chu~~ XL 100ribu nomor : ${XL[$numXL]}.. Pulsanya tinggal ${XLPulsaArray[$numXL]}', 'BashAdmin');" | mysql -h1.1.1.107 -uroot -pc3rmat sms
		fi
	else
		echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('$TUKANGKETIK', '${XL[$numXL]} gagal cek pulsa', 'BashAdmin');" | mysql -h1.1.1.107 -uroot -pc3rmat sms
	fi
	numXL=$((numXL + 1))
done

# alert Paket Habis XL
if [[ $NOW -ge $NEXT_UPDATE_XL ]]; then
	newDate=$(date -d "1 months" +%Y%m%d)
	echo "$newDate">paketHabisXL.txt
	echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ($TUKANGKETIK, ${XL[$numXL]} perpanjang paket... coba cek..!!!, BashAdmin);"| mysql -h1.1.1.107 -uroot -pc3rmat sms
	echo "Resarting openvox..."
	# echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('${XL[0]}', 'system restart c3rmat', 'BashAdmin');"| mysql -h1.1.1.107 -uroot -pc3rmat sms
	sleep 3m
	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.5 -p12345 "asterisk -rx 'gsm send USSD 1 *123*4*4*1*3*2#'")
	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.5 -p12345 "asterisk -rx 'gsm send USSD 2 *123*4*4*1*3*2#'")
	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.5 -p12345 "asterisk -rx 'gsm send USSD 3 *123*4*4*1*3*2#'")
	# echo $(sshpass -pc3rmat ssh -o StrictHostKeyChecking=no admin@3.3.3.5 -p12345 "asterisk -rx 'gsm send USSD 4 *123*4*4*1*3*2#'")
fi

numIndosat=0
#alert Pulsa Indosat
for i in "${failCheckingIndosat[@]}"
do
	if [ "${failCheckingIndosat[$numIndosat]}" = "success" ]; then
		if [[ ${indosatPulsaArray[$numIndosat]} -lt $HARGA_PAKET_INDOSAT ]]; then
			echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('$TUKANGPULSA', 'Pika.. Pika.. Minta pulsa chu~~ Indosat 100ribu nomor : ${INDOSAT[$numIndosat]}.. Pulsanya tinggal ${indosatPulsaArray[$numIndosat]}', 'BashAdmin');" | mysql -h1.1.1.107 -uroot -pc3rmat sms
		fi
	else
		echo "INSERT INTO outbox (DestinationNumber, TextDecoded, CreatorID) VALUES ('$TUKANGKETIK', '${INDOSAT[$numIndosat]} gagal cek pulsa', 'BashAdmin');" | mysql -h1.1.1.107 -uroot -pc3rmat sms
	fi
	numIndosat=$((numIndosat + 1))
done