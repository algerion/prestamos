<!--DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"-->
<html>
	<com:THead>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="shortcut icon" href="images/logoizq.jpg" type="image/x-icon" />
		<com:TClientScript PradoScripts="prado" />
		<com:TStyleSheet ID="stylsht" StyleSheetUrl="librerias/estilos.css" />
		<com:TClientScript ID="jsDateValid" ScriptUrl="librerias/datevalid.js" />
		<com:TStyleSheet ID="stlshGoogie" StyleSheetUrl="../compartidos/googiespell/googiespell.css" />
		<com:TClientScript ID="jsGoogieAJS" ScriptUrl="../compartidos/googiespell/AJS.js" />
		<com:TClientScript ID="jsGoogieSpell" ScriptUrl="../compartidos/googiespell/googiespell.js" />
		<com:TClientScript ID="jsGoogieCookie" ScriptUrl="../compartidos/googiespell/cookiesupport.js" />
		<com:TClientScript ID="jsSpell" ScriptUrl="librerias/spell.js" />
		<com:TContentPlaceHolder ID="Cabeceras" />
	</com:THead>
	<body>
		<com:TForm>
			<div id="page" align="center">
				<com:TTable Width="1000" Style="text-align:center" BorderWidth="0" CellPadding="0" CellSpacing="0" BackColor="#F7F8FC">
					<div id="header">
						<com:TTableRow>
							<com:TTableCell RowSpan="5" Style="background-image:url(images/borde.gif)" />
							<com:TTableCell>
								<com:TPanel ID="pnlEncabezado">
									<com:TImage ImageUrl="images/banner.png" />
								</com:TPanel>
							</com:TTableCell>
							<com:TTableCell RowSpan="5" Style="background-image:url(images/borde.gif)" />
						</com:TTableRow>
					</div>

					<div id="main">
						<com:TTableRow>
							<com:TTableCell>
								<com:TContentPlaceHolder ID="Main" />
							</com:TTableCell>
						</com:TTableRow>
					</div>


					<div id="footer">
						<com:TTableRow>
							<com:TTableCell Style="background-image:url(images/pie.png)" width="1000" height="20" />
						</com:TTableRow>
					</div>
				</com:TTable>
			</div>
		</com:TForm>
	</body>
</html>