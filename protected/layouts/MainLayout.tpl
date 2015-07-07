<!--DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"-->
<!--html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"-->
<html>
	<com:THead>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="shortcut icon" href="images/logoizq.jpg" type="image/x-icon" />
		<com:TStyleSheet ID="stylsht" StyleSheetUrl="librerias/estilos.css" />
		<com:TStyleSheet ID="stlshGoogie" StyleSheetUrl="../compartidos/googiespell/googiespell.css" />
	</com:THead>
	<body <com:TLiteral ID="litBody" />>
		<!--com:TClientScript ID="jsNieve" ScriptUrl="../compartidos/nieve/nieve.js" /-->
		<!--com:TClientScript ID="jsWZDragDrop" ScriptUrl="../compartidos/wz_dragdrop/wz_dragdrop.js" /-->
		<com:TForm>
			<com:TClientScript PradoScripts="prado" />
			<com:TClientScript ID="jsDateValid" ScriptUrl="librerias/datevalid.js" />
			<com:TClientScript ID="jsGoogieAJS" ScriptUrl="../compartidos/googiespell/AJS.js" />
			<com:TClientScript ID="jsGoogieSpell" ScriptUrl="../compartidos/googiespell/googiespell.js" />
			<com:TClientScript ID="jsGoogieCookie" ScriptUrl="../compartidos/googiespell/cookiesupport.js" />
			<com:TClientScript ID="jsSpell" ScriptUrl="librerias/spell.js" />
			<com:TContentPlaceHolder ID="Cabeceras" />
			<div id="page" align="center">
				<com:TTable Width="810" Style="text-align:center" BorderWidth="0" CellPadding="0" CellSpacing="0" BackColor="#F7F8FC">
					<div id="header">
						<com:TTableRow>
							<com:TTableCell RowSpan="5" Style="background-image:url(images/borde.gif)" />
							<com:TTableCell>
								<com:TPanel ID="pnlEncabezado">
									<!-- <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
										codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0"
										width="800" height="144">
										<param name="movie" value="images/fotos.swf">
										<param name="quality" value="high">
										<embed src="images/fotos.swf" quality="high"
											pluginspage="http://www.macromedia.com/go/getflashplayer"
											type="application/x-shockwave-flash" width="800" height="144" />
									</object> -->
									<com:TImage ImageUrl="images/banner.png" />
								</com:TPanel>
							</com:TTableCell>
							<com:TTableCell RowSpan="5" Style="background-image:url(images/borde.gif)" />
						</com:TTableRow>
						<!-- <com:TTableRow>
							<com:TTableCell Style="background-image:url(images/barraBanne.gif)" Width="800" Height="18">&nbsp;
							</com:TTableCell>
						</com:TTableRow> -->
					</div>

					<div id="menu">
						<com:TTableRow>
							<com:TTableCell HorizontalAlign="Center">
								<com:TClientScript>
									var myThemeOfficeBase = '../compartidos/PradoCookMenu/JSCookMenu/ThemeOffice/';
								</com:TClientScript>
								<com:JsCookMenu ID="mnuPrincipal" ThemeName="ThemeOffice"
										JsCookMenuPath="../compartidos/PradoCookMenu/JSCookMenu/JSCookMenu.js"
										StyleSheetThemePath="../compartidos/PradoCookMenu/JSCookMenu/ThemeOffice/theme.css"
										JsThemePath="../compartidos/PradoCookMenu/JSCookMenu/ThemeOffice/theme.js"
										MenuOrientation="hbr" />
								<br />
							</com:TTableCell>
						</com:TTableRow>
					</div>

					<div id="main">
						<com:TTableRow>
							<com:TTableCell>
								<com:TContentPlaceHolder ID="Main" />
								<com:TPanel ID="pnlCerrar">
									<br /><br />
									<com:TActiveButton ID="btnJSCerrar" Text="Cerrar" Attributes.onclick="javascript:window.close();" />
								</com:TPanel>
							</com:TTableCell>
						</com:TTableRow>
					</div>


					<div id="footer">
						<com:TTableRow>
							<com:TTableCell Style="background-image:url(images/pie.png)" width="800" height="20" />
						</com:TTableRow>
					</div>
				</com:TTable>
			</div>
		</com:TForm>
	</body>
</html>