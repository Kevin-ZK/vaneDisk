$(document).ready(function(){
	var params = OC.Util.History.parseUrlQuery();

	// Hack to add a trusted domain
	if (params.trustDomain) {
		OC.dialogs.confirm(t('settings', 'Are you really sure you want add "{domain}" as trusted domain?',
				{domain: params.trustDomain}),
			t('settings', 'Add trusted domain'), function(answer) {
				if(answer) {
					$.ajax({
						type: 'POST',
						url: OC.generateUrl('settings/admin/security/trustedDomains'),
						data: { newTrustedDomain: params.trustDomain }
					}).done(function() {
						window.location.replace(OC.generateUrl('settings/admin'));
					});
				}
			});
	}


	$('#excludedGroups').each(function (index, element) {
		OC.Settings.setupGroupsSelect($(element));
		$(element).change(function(ev) {
			var groups = ev.val || [];
			if (groups.length > 0) {
				groups = ev.val.join(','); // FIXME: make this JSON
			} else {
				groups = '';
			}
			OC.AppConfig.setValue('core', $(this).attr('name'), groups);
		});
	});


	$('#loglevel').change(function(){
		$.post(OC.generateUrl('/settings/admin/log/level'), {level: $(this).val()},function(){
			OC.Log.reload();
		} );
	});

	$('#backgroundjobs span.crondate').tipsy({gravity: 's', live: true});

	$('#backgroundjobs input').change(function(){
		if($(this).attr('checked')){
			var mode = $(this).val();
			if (mode === 'ajax' || mode === 'webcron' || mode === 'cron') {
				OC.AppConfig.setValue('core', 'backgroundjobs_mode', mode);
			}
		}
	});

	$('#shareAPIEnabled').change(function() {
		$('#shareAPI p:not(#enable)').toggleClass('hidden', !this.checked);
	});

	$('#enableEncryption').change(function() {
		$('#encryptionAPI div#EncryptionWarning').toggleClass('hidden');
	});

	$('#reallyEnableEncryption').click(function() {
		$('#encryptionAPI div#EncryptionWarning').toggleClass('hidden');
		$('#encryptionAPI div#EncryptionSettingsArea').toggleClass('hidden');
		OC.AppConfig.setValue('core', 'encryption_enabled', 'yes');
		$('#enableEncryption').attr('disabled', 'disabled');
	});

	$('#startmigration').click(function(event){
		$(window).on('beforeunload.encryption', function(e) {
			return t('settings', 'Migration in progress. Please wait until the migration is finished');
		});
		event.preventDefault();
		$('#startmigration').prop('disabled', true);
		OC.msg.startAction('#startmigration_msg', t('settings', 'Migration started …'));
		$.post(OC.generateUrl('/settings/admin/startmigration'), '', function(data){
			OC.msg.finishedAction('#startmigration_msg', data);
			if (data['status'] === 'success') {
				$('#encryptionAPI div#selectEncryptionModules').toggleClass('hidden');
				$('#encryptionAPI div#migrationWarning').toggleClass('hidden');
			} else {
				$('#startmigration').prop('disabled', false);
			}
			$(window).off('beforeunload.encryption');

		});
	});

	$('#shareAPI input:not(#excludedGroups)').change(function() {
		var value = $(this).val();
		if ($(this).attr('type') === 'checkbox') {
			if (this.checked) {
				value = 'yes';
			} else {
				value = 'no';
			}
		}
		OC.AppConfig.setValue('core', $(this).attr('name'), value);
	});

	$('#shareapiDefaultExpireDate').change(function() {
		$("#setDefaultExpireDate").toggleClass('hidden', !this.checked);
	});

	$('#allowLinks').change(function() {
		$("#publicLinkSettings").toggleClass('hidden', !this.checked);
		$('#setDefaultExpireDate').toggleClass('hidden', !(this.checked && $('#shareapiDefaultExpireDate')[0].checked));
	});

	$('#mail_smtpauth').change(function() {
		if (!this.checked) {
			$('#mail_credentials').addClass('hidden');
		} else {
			$('#mail_credentials').removeClass('hidden');
		}
	});

	$('#mail_smtpmode').change(function() {
		if ($(this).val() !== 'smtp') {
			$('#setting_smtpauth').addClass('hidden');
			$('#setting_smtphost').addClass('hidden');
			$('#mail_smtpsecure_label').addClass('hidden');
			$('#mail_smtpsecure').addClass('hidden');
			$('#mail_credentials').addClass('hidden');
		} else {
			$('#setting_smtpauth').removeClass('hidden');
			$('#setting_smtphost').removeClass('hidden');
			$('#mail_smtpsecure_label').removeClass('hidden');
			$('#mail_smtpsecure').removeClass('hidden');
			if ($('#mail_smtpauth').attr('checked')) {
				$('#mail_credentials').removeClass('hidden');
			}
		}
	});

	$('#mail_general_settings_form').change(function(){
		OC.msg.startSaving('#mail_settings_msg');
		var post = $( "#mail_general_settings_form" ).serialize();
		$.post(OC.generateUrl('/settings/admin/mailsettings'), post, function(data){
			OC.msg.finishedSaving('#mail_settings_msg', data);
		});
	});

	$('#mail_credentials_settings_submit').click(function(){
		OC.msg.startSaving('#mail_settings_msg');
		var post = $( "#mail_credentials_settings" ).serialize();
		$.post(OC.generateUrl('/settings/admin/mailsettings/credentials'), post, function(data){
			OC.msg.finishedSaving('#mail_settings_msg', data);
		});
	});

	$('#sendtestemail').click(function(event){
		event.preventDefault();
		OC.msg.startAction('#sendtestmail_msg', t('settings', 'Sending...'));
		$.post(OC.generateUrl('/settings/admin/mailtest'), '', function(data){
			OC.msg.finishedAction('#sendtestmail_msg', data);
		});
	});

	$('#shareapiExcludeGroups').change(function() {
		$("#selectExcludedGroups").toggleClass('hidden', !this.checked);
	});

	// run setup checks then gather error messages
	$.when(
		OC.SetupChecks.checkWebDAV(),
		OC.SetupChecks.checkSetup(),
		OC.SetupChecks.checkGeneric()
	).then(function(check1, check2, check3) {
		var errors = [].concat(check1, check2, check3);
		var $el = $('#postsetupchecks');
		var $errorsEl;
		$el.find('.loading').addClass('hidden');
		if (errors.length === 0) {
		} else {
			$errorsEl = $el.find('.errors');
			for (var i = 0; i < errors.length; i++ ) {
				$errorsEl.append('<li>' + errors[i] + '</li>');
			}
			$errorsEl.removeClass('hidden');
			$el.find('.hint').removeClass('hidden');
		}
	});
});
