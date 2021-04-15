<?php if ( !defined( 'ABSPATH' ) ) die( 'Kein direkter Zugriff erlaubt!' ); ?>
<?php
if ( !current_user_can( 'manage_options' ) ) {
	wp_die( __( 'Du hast keine Berechtigungen, um auf diese Seite zuzugreifen.' ) );
}
?>

<?php
global $wp_roles, $qa_email_notification_subject, $qa_email_notification_content;
$options		 = $this->get_options( 'general_settings' );
$wp_nonce_verify = wp_nonce_field( 'qa-verify', '_wpnonce', true, false );

/**
 * Returns an array of layout options
 *
 * @since 1.4
 */
function qa_layouts() {
	return array(
		'content-sidebar'	 => array(
			'value'		 => 'content-sidebar',
			'label'		 => __( 'Inhalt links', QA_TEXTDOMAIN ),
			'thumbnail'	 => QA_PLUGIN_URL . 'ui-admin/images/content-sidebar.png',
		),
		'sidebar-content'	 => array(
			'value'		 => 'sidebar-content',
			'label'		 => __( 'Inhalt rechts', QA_TEXTDOMAIN ),
			'thumbnail'	 => QA_PLUGIN_URL . 'ui-admin/images/sidebar-content.png',
		),
		'content'			 => array(
			'value'		 => 'content',
			'label'		 => __( 'Einspaltig, keine Seitenleiste', QA_TEXTDOMAIN ),
			'thumbnail'	 => QA_PLUGIN_URL . 'ui-admin/images/content.png',
		),
	);
}

/**
 * Renders the Layout setting field.
 *
 * @since 1.4
 */
function qa_settings_field_layout() {
	global $qa_general_settings;
	foreach ( qa_layouts() as $layout ) {
		?>
		<div class="layout image-radio-option theme-layout">
			<label class="description">
				<input type="radio" name="qa_page_layout" value="<?php echo esc_attr( $layout[ 'value' ] ); ?>" <?php checked( $qa_general_settings[ 'page_layout' ], $layout[ 'value' ], true ); ?> />
				<span>
					<img src="<?php echo esc_url( $layout[ 'thumbnail' ] ); ?>" width="136" height="122" alt="" />
		<?php echo $layout[ 'label' ]; ?>
				</span>
			</label>
		</div>
		<?php
	}
}
?>

<div class="wrap">
<?php screen_icon( 'options-general' ); ?>

	<h2><?php _e( 'PS Q&A Einstellungen', QA_TEXTDOMAIN ); ?></h2>

	<br />
	<span class="description"><?php _e( 'Diese Seite verwendet Ajax. Einstellungen werden ohne Seitenaktualisierung gespeichert.', QA_TEXTDOMAIN ) ?></span>

	<div id="poststuff" class="metabox-holder">

		<form action="" method="post" class="qa-general">
<?php if ( 0 == 1 ) { ?>
				<div class="postbox <?php echo $this->postbox_classes( 'qa_display' ) ?>" id="qa_display">
					<h3 class='hndle'><span><?php _e( 'Einstellungen für die Themeanpassung', QA_TEXTDOMAIN ) ?></span></h3>
					<div class="inside">
						<table class="form-table">

							<tr>
								<td colspan="2">
									<span class="description">
	<?php printf( __( 'PS QA unterstützt diese Themes standardmäßig: <b>%s</b>. Für den Rest der Themes sollte das Aussehen der Q&A-Seiten an Dein Theme angepasst werden. Dies kann durch Bearbeiten der Vorlagen oder durch Anpassen der folgenden Einstellungen erfolgen. Wenn Du keine Anzeigeprobleme hast, kannst Du diese unverändert lassen.', QA_TEXTDOMAIN ), qa_supported_themes() ); ?>
									</span>
								</td>
							</tr>
							<tr>
								<th>
									<label for="page_layout"><?php _e( 'Seitenlayout', QA_TEXTDOMAIN ) ?></label>
								</th>
								<td>
	<?php qa_settings_field_layout() ?>
									<span class="description">
									<?php _e( 'Wähle das Layout aus, das auf Q & A-Seiten angewendet werden soll. Wende für optimale Ergebnisse das gleiche Layout für den Rest Deiner Seiten an.', QA_TEXTDOMAIN ) ?>
									</span>
								</td>
							</tr>

							<tr>
								<th>
									<label for="page_width"><?php _e( 'Verwendbare Seitenbreite (px)', QA_TEXTDOMAIN ) ?></label>
								</th>
								<td>
									<input style="width:100px" name="page_width" value="<?php echo @$options[ 'page_width' ]; ?>" />
									&nbsp;&nbsp;&nbsp;
									<span class="description">
	<?php _e( 'Gib die nutzbare Seitenbreite Deines Themes ein (normalerweise ca. 1000). Aufgrund von Auffüllungen kann die nutzbare Breite geringfügig kleiner sein als die gesamte Seitenbreite. Tipp: Um die verwendbare Seitenbreite zu ermitteln, klicke mit Google Chrome mit der rechten Maustaste links im Menü "Fragen und Antworten" und wähle "Element überprüfen". Die Abmessungen der Nettoseitenbreite werden angezeigt.', QA_TEXTDOMAIN ) ?>
									</span>
								</td>
							</tr>

							<tr>
								<th>
									<label for="content_width"><?php _e( 'Q&A Inhaltsbreite (px)', QA_TEXTDOMAIN ) ?></label>
								</th>
								<td>
									<input style="width:100px" name="content_width" value="<?php echo @$options[ 'content_width' ]; ?>" />
									&nbsp;&nbsp;&nbsp;
									<span class="description">
	<?php _e( 'Gib die gewünschte Breite des Hauptfelds für Fragen und Antworten ein. Die empfohlene Mindestbreite beträgt 584.', QA_TEXTDOMAIN ) ?>
									</span>
								</td>
							</tr>

							<tr>
								<th>
									<label for="content_width"><?php _e( 'Ausrichtung von Q&A-Inhalten', QA_TEXTDOMAIN ) ?></label>
								</th>
								<td>
									<select name="content_alignment" class="qa_content_alignment">
										<option value="center" <?php selected( @$options[ 'content_alignment' ], 'center', true ); ?>><?php _e( 'Zentriert', QA_TEXTDOMAIN ) ?></option>
										<option value="left" <?php selected( @$options[ 'content_alignment' ], 'left', true ); ?>><?php _e( 'Links', QA_TEXTDOMAIN ) ?></option>
										<option value="right" <?php selected( @$options[ 'content_alignment' ], 'right', true ); ?>><?php _e( 'Rechts', QA_TEXTDOMAIN ) ?></option>
									</select>
									&nbsp;&nbsp;&nbsp;
									<span class="description">
	<?php _e( 'Wähle die gewünschte Ausrichtung des Q&A-Inhalts relativ zur Seite und Seitenleiste.', QA_TEXTDOMAIN ) ?>
									</span>
								</td>
							</tr>

							<tr>
								<th>
									<label for="sidebar_width"><?php _e( 'Seitenleistenbreite (px)', QA_TEXTDOMAIN ) ?></label>
								</th>
								<td>
									<input style="width:100px" name="sidebar_width" value="<?php echo @$options[ 'sidebar_width' ]; ?>" />
									&nbsp;&nbsp;&nbsp;
									<span class="description">
	<?php _e( 'Gib die Seitenleistenbreite Deines Themes ein, wenn Du die Seitenleiste ausgewählt hast, die in der Seitenlayouteinstellung angezeigt werden soll. Abhängig von den Rändern musst Du möglicherweise einige Pixel größer als die tatsächliche Breite einstellen. Tipp: Um die Breite Deiner Seitenleiste zu ermitteln, fahre mit Google Chrome mit der rechten Maustaste über Deine Seitenleiste, klicke mit der rechten Maustaste und wähle "Element überprüfen". Die Abmessungen der Seitenleiste werden angezeigt.', QA_TEXTDOMAIN ) ?>
									</span>
								</td>
							</tr>

							<tr>
								<th>
									<label for="auto_css_button"><img class="ajax-loader" src="<?php echo QA_PLUGIN_URL . 'ui-admin/images/ajax-loader.gif'; ?>" /></label>
								</th>
								<td>
									<input type="button" class="button-secondary qa-auto-css" name="save" value="<?php _e( 'Erstelle zusätzliche CSS-Regeln', QA_TEXTDOMAIN ); ?>">
									&nbsp;&nbsp;&nbsp;
									<span class="description">
	<?php _e( 'Wenn Du auf diese Schaltfläche klickst und die oben angegebenen Breiten und das ausgewählte Layout verwendest, werden die CSS-Regeln automatisch angewendet und im Feld "Zusätzliche CSS-Regeln" gespeichert. Diese Einstellungen funktionieren möglicherweise nicht bei jedem Theme und Du musst möglicherweise noch einige Feinabstimmungen vornehmen. Tipp: Wenn das Ergebnis nicht zufriedenstellend ist (z.B. Seitenleiste verschoben), kannst Du die Breiteneinstellungen ändern und es erneut versuchen. Jedes Mal, wenn das Feld Zusätzliche CSS-Regeln zurückgesetzt wird.', QA_TEXTDOMAIN ) ?>
									</span>
									<script type="text/javascript">
										//<![ CDATA[
											jQuery(do c ument).ready(fun ction($) {
												$("inp ut.qa-auto -css").on("click", function(){
												var page_layout = $("input[name='qa _page_layout']:checked");
												var page_width = $("in put[name='page_width']");
												var content_width = $("input [name='content_width']");
												var  content_alignment = $('select.qa_content_ali gnment option:selected');
												var sidebar_width = $("input [name='sidebar_width']");
												var additional_css = $("textarea[ name='additional_css']");
												var confirmed = true;
													if ( $.tr im(additional_css.val()) != '' ) {
												confirmed = false;
										}
													if (  $.trim( p age_width.val()) ==  ''){
													alert('<?php echo esc_js( __( 'Die Seitenbreite darf nicht leer sein', QA_TEXTDOMAIN ) ) ?>');
																	page_width.focus();
																return false;
									 }
																	else if ( $.t rim(con t ent_width.val()) ==  ''){
													alert('<?php echo esc_js( __( 'Die Inhaltsbreite darf nicht leer sein', QA_TEXTDOMAIN ) ) ?>');
																	content_width.focus();
																return false;
									 }
																	else if ( page_layout.val()  != 'content' && $.t rim(sid e bar_width.val()) ==  ''){
													alert('<?php echo esc_js( __( 'Mit dem ausgewählten Seitenlayout darf die Seitenleistenbreite nicht leer sein', QA_TEXTDOMAIN ) ) ?>');
																	sidebar_width.focus();
																return false;
									 }
																	else if ( pa rseInt(page_w idth.val()) < parse Int(content_w idth.val()) + parse Int (sidebar_width.val() ) ){
													alert('<?php echo esc_js( __( 'Die Seitenbreite darf nicht kleiner sein als die Inhaltsbreite + Seitenleistenbreite', QA_TEXTDOMAIN ) ) ?>');
																	sidebar_width.focus();
																return false;
									 }
																	else if ( !confirmed ) {
																	if ( confirm('<?php echo esc_js( __( 'Dein zusätzliches Feld für CSS-Regeln ist nicht leer. Wenn Du fortfährst, wird der vorhandene Wert überschrieben. Bist du sicher?', QA_TEXTDOMAIN ) ) ?>') ) { 	 											confirmed = true;
																		}
													else {
																return false;
																}
										}
																	if ( confi rmed ) {
																	$('.ajax-loader').show(); 	 							 			var data = {action: 'qa- estimate', page_layout:page_la yout.val(), page_width:page_widt h.val(), content_width:content_width.va l(), content_alignment:content_alignmen t.val(), sidebar_width:sidebar_width.val(), nonce: '<?php echo wp_create_nonce() ?> '};
																						$.post(a jaxurl,  data, function(resp onse) {
																						$('.ajax-loader').hide();
																							if ( response && response.error )  {
																						alert(response.error);
																							} 								 		 	else if ( res ponse && response. css ){
																							$("textarea[ name=' additional_c ss']").val(response.css );
															alert('<?php echo esc_js( __( 'Zusätzliche CSS-Regeln eingetragen und gespeichert. Jetzt kannst Du die Anzeige von QS-Seiten in verschiedenen Browsern überprüfen.', QA_TEXTDOMAIN ) ) ?>');
																					}
														else  {
															alert('<?php echo esc_js( __( 'Ein Verbindungsfehler ist aufgetreten. Bitte versuche es erneut.', QA_TEXTDOMAIN ) ) ?>');
														}
																	},'json');
																}
																});
										});
										//]]>
									</script>

								</td>
							</tr>

							<tr>
								<th>
									<label for="additional_css"><?php _e( 'Zusätzliche CSS-Regeln', QA_TEXTDOMAIN ) ?></label>
								</th>
								<td>
									<textarea class="qa-full" rows="2" name="additional_css"><?php echo @$options[ 'additional_css' ]; ?></textarea>
									<br />
									<span class="description">
	<?php _e( 'Du kannst Deine CSS-Codes manuell hinzufügen oder die bereits eingetragenen bearbeiten. Stelle sicher, dass Du gültiges CSS verwendest. z.B.', QA_TEXTDOMAIN ) ?>&nbsp;<code>#sidebar{width:200px;float:left;}</code>
									</span>

								</td>
							</tr>

							<tr>
								<th>
									<label for="search_input_width"><?php _e( 'Suche Eingabefeldbreite (px)', QA_TEXTDOMAIN ) ?></label>
								</th>
								<td>
									<input style="width:100px" name="search_input_width" value="<?php echo @$options[ 'search_input_width' ]; ?>" />
									&nbsp;&nbsp;&nbsp;
									<span class="description">
	<?php _e( 'Wenn das Sucheingabefeld unter dem Q&A-Menü angezeigt wird, reduziere diesen Wert.', QA_TEXTDOMAIN ) ?>
									</span>
								</td>
							</tr>

						</table>
					</div>
				</div>

<?php } ?>
			<p class="submit">
			<?php echo $wp_nonce_verify; ?>
				<input type="hidden" name="action" value="qa-save" />
				<input type="hidden" name="key" value="general_settings" />
				<input type="submit" class="button-primary" name="save" value="<?php _e( 'Speichere alle Einstellungen auf dieser Seite', QA_TEXTDOMAIN ); ?>">
				<img class="ajax-loader" src="<?php echo QA_PLUGIN_URL . 'ui-admin/images/ajax-loader.gif'; ?>" />
				<span style="display:none;font-weight:bold;color:darkgreen" class="qa_settings_saved"><?php _e( 'Einstellungen gespeichert', QA_TEXTDOMAIN ); ?></span>
			</p>

			<div class="postbox <?php echo $this->postbox_classes( 'qa_display' ) ?>" id="qa_display">
				<h3 class='hndle'><span><?php _e( 'Andere Anzeigeeinstellungen', QA_TEXTDOMAIN ) ?></span></h3>


				<div class="inside">

					<table class="form-table">
						<tr>
							<th>
								<label for="questions_per_page"><?php _e( 'Fragen pro Seite', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<input style="width:100px" name="questions_per_page" value="<?php echo @$options[ 'questions_per_page' ]; ?>" />&nbsp;&nbsp;&nbsp;<span class="description"><?php echo __( 'Wenn leer, wird die WP-Einstellung verwendet: ', QA_TEXTDOMAIN ) . get_option( 'posts_per_page' ); ?></span>
								<br />
								<span class="description">
<?php printf( __( 'WICHTIG: Fragen pro Seite dürfen aufgrund von WP-Einschränkungen nicht unter der Einstellung von Wordpress %s liegen. Wenn Du es so einstellst, wird stattdessen die Wordpress-Einstellung verwendet.', QA_TEXTDOMAIN ), '<a href="' . admin_url( 'options-reading.php' ) . '">' . __( 'Blogseiten zeigen höchstens', QA_TEXTDOMAIN ) . '</a>' ); ?>
								</span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="answers_per_page"><?php _e( 'Antworten pro Seite', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<input style="width:100px" name="answers_per_page" value="<?php echo @$options[ 'answers_per_page' ]; ?>" />&nbsp;&nbsp;&nbsp;<span class="description"><?php _e( 'Wenn leer gelassen: 20', QA_TEXTDOMAIN ); ?></span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="disable_editor"><?php _e( 'Deaktiviere den WP-Editor', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<input type="checkbox" name="disable_editor" <?php if ( @$options[ "disable_editor" ] ) echo "checked='checked'"; ?> />
								&nbsp;&nbsp;&nbsp;
								<span class="description">
<?php _e( 'Wenn Du Probleme mit Buddypress hast oder nicht möchtest, dass Einsendungen formatiert werden, aktiviere dieses Kontrollkästchen. Anschließend wird der Textbereich anstelle des WP-Editors für Frage- und Antwortformulare verwendet.', QA_TEXTDOMAIN ); ?>
								</span>
							</td>
						</tr>

					</table>
				</div>
			</div>

			<p class="submit">
				<input type="submit" class="button-primary" name="save" value="<?php _e( 'Alle Einstellungen auf dieser Seite speichern', QA_TEXTDOMAIN ); ?>">
				<img class="ajax-loader" src="<?php echo QA_PLUGIN_URL . 'ui-admin/images/ajax-loader.gif'; ?>" />
				<span style="display:none;font-weight:bold;color:darkgreen" class="qa_settings_saved"><?php _e( 'Einstellungen gespeichert', QA_TEXTDOMAIN ); ?></span>
			</p>

			<div class="postbox <?php echo $this->postbox_classes( 'qa_access' ) ?>" id="qa_access">
				<h3 class='hndle'><span><?php _e( 'Eingabehilfeneinstellungen', QA_TEXTDOMAIN ) ?></span></h3>


				<div class="inside">

					<table class="form-table">

						<tr>
							<th>
								<label for="roles"><?php _e( 'Funktionsberechtigungen zuweisen', QA_TEXTDOMAIN ) ?></label>
								<img class="ajax-loader" src="<?php echo QA_PLUGIN_URL . 'ui-admin/images/ajax-loader.gif'; ?>" />
							</th>
							<td>
								<select id="roles" name="roles">
<?php wp_dropdown_roles( @$options[ "selected_role" ] ); ?>
								</select>
								<span class="description"><?php _e( 'Diese Liste enthält alle Benutzerrollen Deiner Webseite. Wenn Du eine neue Auswahl triffst, wird die Fähigkeit dieser Rolle angezeigt. Wähle eine Rolle aus, der Du PS Q&A-Funktionen zuweisen möchtest.', QA_TEXTDOMAIN ); ?></span>

								<br /><br />

								<div id="capabilities">
<?php foreach ( $GLOBALS[ '_qa_core_admin' ]->capability_map as $capability => $description ): ?>
										<input id="<?php echo $capability ?>_checkbox" type="checkbox" name="capabilities[<?php echo $capability; ?>]" value="1" />
										<span class="description <?php echo $capability ?>"><?php echo $description; ?></span>
										<br />
<?php endforeach; ?>
								</div>
							</td>
						</tr>

						<tr>
							<th>
								<label for="visitor_method"><?php _e( 'Nachdem der Besucher eine Frage oder Antwort eingereicht hat', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<select id="visitor_method" name="method">
<?php
if ( isset( $options[ 'method' ] ) )
	$method		 = $options[ 'method' ];
else
	$method		 = '';
?>
									<option value="claim" <?php if ( $method != 'assign' ) echo "selected='selected'" ?>><?php _e( 'Er wird um Registrierung gebeten', QA_TEXTDOMAIN ) ?></option>
									<option value="assign" <?php if ( $method == 'assign' ) echo "selected='selected'" ?>><?php _e( 'Die Frage wird einem Benutzer zugewiesen', QA_TEXTDOMAIN ) ?></option>
								</select>
								&nbsp;
								<span id="assigned_to" <?php if ( $method != 'assign' ) echo "style='display:none'" ?>>
<?php
if ( isset( $options[ 'assigned_to' ] ) )
	$selected	 = $options[ 'assigned_to' ];
else
	$selected	 = 0;
_e( 'Zuweisen: ', QA_TEXTDOMAIN );
wp_dropdown_users( array( 'name' => 'assigned_to', 'selected' => $selected ) );
?>
								</span>
								<br />
								<span class="description">
<?php _e( 'Jede Frage und Antwort sollte einen Autor haben. Wenn Du möchtest, dass der Besucher eine Frage oder Antwort ohne Registrierung sendet, kannst Du einen voreingestellten Autor zuweisen.', QA_TEXTDOMAIN ) ?>
								</span>
							</td>
						</tr>


						<tr>
							<th>
								<label for="thank_you_page"><?php _e( 'Danke Seite', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
<?php
if ( isset( $options[ 'thank_you' ] ) )
	$selected	 = $options[ 'thank_you' ];
else
	$selected	 = 0;
wp_dropdown_pages( array( 'name' => 'thank_you', 'selected' => $selected ) );
?>
								<br />
								<span class="description">
									<?php _e( 'Wenn Fragen gespeichert und als ausstehend gespeichert werden, wird der Benutzer nach dem Absenden einer Frage oder Antwort auf diese Seite weitergeleitet.', QA_TEXTDOMAIN ) ?>
								</span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="unauthorized"><?php _e( 'Seite für nicht autorisierten Zugriff', QA_TEXTDOMAIN ) ?></label>
							</th>

							<td>
								<?php
								if ( isset( $options[ 'unauthorized' ] ) )
									$selected	 = $options[ 'unauthorized' ];
								else
									$selected	 = 0;
								wp_dropdown_pages( array( 'name' => 'unauthorized', 'selected' => $selected ) );
								?>
								<br />
								<span class="description">
									<?php _e( 'Wenn ein Benutzer versucht, auf eine Seite zuzugreifen, auf die er nicht zugreifen sollte, wird er stattdessen auf diese Seite umgeleitet.', QA_TEXTDOMAIN ) ?>
								</span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="report"><?php _e( 'Meldegründe', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<input type="text" style="width:200px" name="report_reasons" value="<?php if ( isset( $options[ "report_reasons" ] ) ) echo stripslashes( $options[ "report_reasons" ] ) ?>" />
								<br />
								<span class="description">
									<?php _e( 'Gib die vom Benutzer zu wählenden Berichtsgründe ein, die jeweils durch Komma getrennt sind, z.B. Spam, Sprache. Wenn das Feld leer bleibt, wird der Benutzer nicht aufgefordert, einen Berichtsgrund auszuwählen.', QA_TEXTDOMAIN ) ?>
								</span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="report"><?php _e( 'Verwende Captcha', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<input type="checkbox" name="captcha" value="1" <?php if ( isset( $options[ "captcha" ] ) && $options[ "captcha" ] ) echo 'checked="checked"' ?> />
								&nbsp;
								<span class="description">
									<?php _e( 'Gibt an, ob beim Senden die Captcha-Überprüfung verwendet werden soll.', QA_TEXTDOMAIN ) ?>
									<?php
									if ( !qa_is_captcha_usable() )
										_e( 'Hinweis: Deine PHP-Installation lässt Captcha nicht zu.', QA_TEXTDOMAIN );
									?>
								</span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="report"><?php _e( 'E-Mail-Adresse im Bericht', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<input type="text" style="width:200px" name="report_email" value="<?php if ( isset( $options[ "report_email" ] ) ) echo $options[ "report_email" ] ?>" />
								<br />
								<span class="description">
									<?php _e( 'E-Mail-Adresse, die benachrichtigt wird, wenn eine Frage oder Antwort gemeldet wird. Wenn Sie das Feld leer lassen, wird die Benachrichtigung deaktiviert. Hinweis: Wenn eine Frage oder Antwort mehrmals gemeldet wird, wird nur der erste Bericht per E-Mail gesendet, aber die Anzahl der Berichte und der letzte Reporter werden gespeichert.', QA_TEXTDOMAIN ) ?>
								</span>
							</td>
						</tr>


						<?php
						global $bp;
						if ( is_object( $bp ) ) :
							?>
							<tr>
								<th>
									<label for="bp_comment_hide"><?php _e( 'Antwort im Aktivitätsstrom deaktivieren', QA_TEXTDOMAIN ) ?></label>
								</th>
								<td>
									<input type="checkbox" name="bp_comment_hide" value="1" <?php if ( @$options[ "bp_comment_hide" ] ) echo "checked='checked'" ?>/>
									&nbsp;&nbsp;&nbsp;
									<span class="description">
										<?php _e( 'Wenn Du dies aktivierst, wird das Kommentieren der Benachrichtigung über die gestellte Frage in Buddypress Activity Stream deaktiviert, sodass der Benutzer die Frage über vom Plugin generierte Seiten beantworten kann.', QA_TEXTDOMAIN ) ?>
									</span>
								</td>
							</tr>

						<?php endif; ?>
					</table>
				</div>
			</div>

			<p class="submit">
				<input type="submit" class="button-primary" name="save" value="<?php _e( 'Alle Einstellungen auf dieser Seite speichern', QA_TEXTDOMAIN ); ?>">
				<img class="ajax-loader" src="<?php echo QA_PLUGIN_URL . 'ui-admin/images/ajax-loader.gif'; ?>" />
				<span style="display:none;font-weight:bold;color:darkgreen" class="qa_settings_saved"><?php _e( 'Einstellungen gespeichert', QA_TEXTDOMAIN ); ?></span>
			</p>

			<div class="postbox <?php echo $this->postbox_classes( 'qa_notification' ) ?>" id="qa_notification">
				<h3 class='hndle'><span><?php _e( 'Benachrichtigungseinstellungen', QA_TEXTDOMAIN ) ?></span></h3>


				<div class="inside">

					<table class="form-table">

						<tr>
							<th><label for="cc_admin"><?php _e( 'CC den Administrator:', QA_TEXTDOMAIN ); ?></label></th>
							<td>
								<input type="hidden" name="qa_cc_admin" value="0" />
								<input type="checkbox" id="qa_cc_admin" name="qa_cc_admin" value="1" <?php checked( get_option( 'qa_cc_admin', '0' ) ); ?> />
								<span class="description"><?php _e( 'cc den Administrator', QA_TEXTDOMAIN ); ?></span>
							</td>
						</tr>
						<tr>
							<th>
								<label for="qa_email_notification_subject"><?php _e( 'Benachrichtigung E-Mail Betreff', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<input style="width:200px" id="qa_email_notification_subject" name="qa_email_notification_subject" value="<?php echo get_option( 'qa_email_notification_subject', $qa_email_notification_subject ); ?>" />
								<br/>
								<span class="description">
									<?php _e( 'Variablen:', 'messaging' ); ?> SITE_NAME
								</span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="qa_email_notification_content"><?php _e( 'Benachrichtigung E-Mail-Inhalt', QA_TEXTDOMAIN ) ?></label>
							</th>
							<td>
								<textarea class="qa-full" id="qa_email_notification_content" name="qa_email_notification_content" rows="6" cols="120"><?php echo get_option( 'qa_email_notification_content', $qa_email_notification_content ); ?></textarea>
								<br/>
								<span class="description">
									<?php _e( 'Variablen:', 'messaging' ); ?> TO_USER, SITE_NAME, SITE_URL, QUESTION_TITLE, QUESTION_DESCRIPTION, QUESTION_LINK
								</span>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<p class="submit">
				<input type="submit" class="button-primary" name="save" value="<?php _e( 'Alle Einstellungen auf dieser Seite speichern', QA_TEXTDOMAIN ); ?>">
				<img class="ajax-loader" src="<?php echo QA_PLUGIN_URL . 'ui-admin/images/ajax-loader.gif'; ?>" />
				<span style="display:none;font-weight:bold;color:darkgreen" class="qa_settings_saved"><?php _e( 'Einstellungen gespeichert', QA_TEXTDOMAIN ); ?></span>
			</p>


		</form>
	</div>
</div>
