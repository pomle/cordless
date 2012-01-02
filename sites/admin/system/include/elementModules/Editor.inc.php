<?
$Editor = new \Element\TextEditor($args[0], $args[1], 80, 30);
$Editor->addClass('populusLimit');

$ToolBar = new \Element\ToolBar('tag', _('HTML Taggar'));
$ToolBar->addTools(
	\Element\Tool::textInsert('text_paragraph', _('Paragraf'), 'p'),

	\Element\Tool::textInsert('text_bold', _('Fetstil'), 'b'),
	\Element\Tool::textInsert('text_italic', _('Kursiv'), 'i'),
	\Element\Tool::textInsert('text_underline', _('Understruket'), 'u'),

	\Element\Tool::textInsert('text_list_numbers', _('Numrerad lista'), 'ol'),
	\Element\Tool::textInsert('text_list_bullets', _('Onumrerad lista'), 'ul')
);
$Editor->addToolBar($ToolBar);

$ToolBar = new \Element\ToolBar('script_code', _('HTML Taggar'));
$ToolBar->addTools(
	\Element\Tool::popupTrigger('TextEdit.HTMLSafe', 'html_valid', _('HTML-säkra tecken')),
	\Element\Tool::popupTrigger('TextEdit.URL', 'world_link', _('Länk')),
	\Element\Tool::popupTrigger('TextEdit.ProductURL', 'car_link', _('Produktlänk'))
);
$Editor->addToolBar($ToolBar);

echo $Editor;