-------------------------------Prueba placetopay-----------------------------
Este repositorio contiene los archivos desarrollados por juan diego tr�chez M.
en respuesta de la prueba enviada por Placetopay.

->La p�gina principal es testpay.php

->La p�gina phptransactiongo.php realiza la transacci�n cuando la p�gina
 principal le env�a los datos por post y retorna a la p�gina principal
 entregando los valores de respuesta del servicio de transacci�n.

->Para garantizar el almacenamiento de informaci�n en cache es necesario
 tener instalado el m�dulo php php_apcu, De lo contrario la informaci�n que
 se deb�a guardar en cache es almacenada en forma de cookies

->La p�gina formulario muestra todas las transacciones realizadas seguidamente.
 Si se inicia el procedimiento de pago desde  cero (cargar la p�gina sin
 par�metros GET), la lista de transacci�n se inicializa de nueva.

***************Documento escrito por Juan Diego Tr�chez Montoya****************
///////////////////////juan.trochez@correounivalle.edu.co//////////////////////