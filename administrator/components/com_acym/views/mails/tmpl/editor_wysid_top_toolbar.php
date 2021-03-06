<?php
defined('_JEXEC') or die('Restricted access');
?><div id="acym__wysid__top-toolbar" class="grid-x cell">
	<div class="cell grid-x" id="acym__wysid__top-toolbar__actions">
		<div class="cell grid-x align-left small-4 acym_vcenter ">
			<i id="acym__wysid__top-toolbar__undo" class="cell shrink acymicon-undo acym__wysid__top-toolbar__icon"></i>
			<p class="cell shrink">|</p>
			<i id="acym__wysid__top-toolbar__redo" class="cell shrink acymicon-repeat acym__wysid__top-toolbar__icon"></i>
		</div>
		<div class="cell grid-x auto small-4 hide-for-small-only align-center acym_vcenter">
			<p class="cell shrink acym__wysid__top-toolbar__icon margin-right-1"><?php echo acym_translation('ACYM_PREVIEW'); ?></p>
			<i id="acym__wysid__view__desktop" class="cell shrink acymicon-desktop text-center acym__wysid__top-toolbar__icon"></i>
			<i id="acym__wysid__view__smartphone" class="cell shrink acymicon-mobile text-center acym__wysid__top-toolbar__icon"></i>
		</div>
		<div class="cell grid-x small-4 align-right">
            <?php
            $ctrl = acym_getVar('cmd', 'ctrl', 'dashboard');
            if (acym_isAdmin() && 'campaigns' === $ctrl) {
                ?>
				<button id="acym__wysid__saveastmpl__button" type="button" class="cell small-6 medium-shrink margin-bottom-0 margin-right-0"><i class="acymicon-save acym__wysid__top-toolbar__button__icon" data-acym-tooltip="<?php echo acym_translation('ACYM_SAVE_AS_TMPL'); ?>"></i></button>
            <?php } ?>
			<button id="acym__wysid__test__button" type="button" class="cell small-6 medium-shrink margin-bottom-0"><i class="acymicon-send-o acym__wysid__top-toolbar__button__icon" data-acym-tooltip="<?php echo acym_translation('ACYM_SEND_TEST'); ?>"></i></button>
			<p class="acym__color__white margin-bottom-0 margin-right-1" style="font-size: 26px">|</p>
			<button id="acym__wysid__cancel__button" type="button" class="cell small-6 medium-shrink margin-bottom-0 margin-right-1"><i class="acymicon-ban acym__wysid__top-toolbar__button__icon" data-acym-tooltip="<?php echo acym_translation('ACYM_CANCEL'); ?>"></i></button>
			<button id="acym__wysid__save__button" type="button" class="cell small-6 medium-shrink margin-bottom-0"><i class="acymicon-check-circle acym__wysid__top-toolbar__button__icon" data-acym-tooltip="<?php echo acym_translation('ACYM_APPLY'); ?>"></i></button>
		</div>
	</div>
	<div class="cell grid-x align-left acym_vcenter" id="acym__wysid__top-toolbar__notification">
		<i class="cell shrink fa" id="acym__wysid__top-toolbar__notification__icon"></i>
		<div class="cell auto margin-left-1 margin-right-1 grid-x acym_vcenter" id="acym__wysid__top-toolbar__notification__message"></div>
		<i class="cell grid-x shrink acymicon-close cursor-pointer margin-right-1" id="acym__wysid__top-toolbar__notification__close"></i>
		<div class="cell shrink grid-x" id="acym__wysid__top-toolbar__keep"><i class="acymicon-check-circle"></i></div>
	</div>
</div>
<div id="acym__wysid__text__tinymce__editor"></div>

