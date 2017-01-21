<?php /* Smarty version Smarty-3.1.19, created on 2017-01-20 09:57:29
         compiled from "/usr/share/nginx/html/prestashop/adminacme/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2084744977588225593b85d3-02814557%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8a2ac868e85aec78353073194e868560e9d43f62' => 
    array (
      0 => '/usr/share/nginx/html/prestashop/adminacme/themes/default/template/content.tpl',
      1 => 1482131820,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2084744977588225593b85d3-02814557',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_588225593ba107_51779967',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_588225593ba107_51779967')) {function content_588225593ba107_51779967($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
