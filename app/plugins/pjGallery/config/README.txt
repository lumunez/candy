1. How to install a plugin
-----------------------------------------------
	1.1 Before script installation
		- Copy the plugin folder and paste it into 'app/plugins/'
		- Do the same for all the plugins you need, then install the script
	
	1.2 After script installation
		- Copy the plugin folder and paste it into 'app/plugins/'
		- Manually run the plugin *.sql file(s), located in 'app/plugins/PLUGIN_NAME/config/'


2. How to enable a plugin
-----------------------------------------------
	Add plugin name to $CONFIG['plugins'] array into 'app/config/config.inc.php' and 'app/config/config.sample.php'
	For example: 
	<?php
	$CONFIG['plugins'] = array('pjAdminGalleries', 'pjAdminBackup');
	//-- OR -- 
	$CONFIG['plugins'] = 'pjAdminGalleries';
	?>


3. How to access a plugin
-----------------------------------------------
	For example:
	index.php?controller=pjAdminBackup
	index.php?controller=PLUGIN_NAME&action=SOME_ACTION
	
	Add above url as hyperlink to the menu if you need to.
	

4. How to use a plugin accross the script
-----------------------------------------------
	4.1 Into controllers
		
		- Using the plugin model
		
			pjObject::import('Model', 'pjAdminGalleries:pjGallery');
			$data = pjGalleryModel::factory()->findAll()->getData();

		- Using the plugin resources
		
			$this->appendCss('pj-gallery.css', pjObject::getConstant('pjAdminGalleries', 'PLUGIN_CSS_PATH'));
			$this->appendJs('jquery.gallery.js', pjObject::getConstant('pjAdminGalleries', 'PLUGIN_JS_PATH'));
			
	4.2 Into presentation layer (views, *.js)
		
		// *.php
		<div id="gallery"></div>
		<script>
		var pjGallery = pjGallery || {};
		pjGallery.foreign_id = "<?php echo $tpl['arr']['id']; ?>";
		</script>
		
		
		// *.js
		// Make sure document is ready
				
		$("#gallery").gallery({
			compressUrl: "index.php?controller=pjAdminGalleries&action=compressGallery&foreign_id=" + pjGallery.foreign_id,
			getUrl: "index.php?controller=pjAdminGalleries&action=getGallery&foreign_id=" + pjGallery.foreign_id,
			deleteUrl: "index.php?controller=pjAdminGalleries&action=deleteGallery",
			emptyUrl: "index.php?controller=pjAdminGalleries&action=emptyGallery&foreign_id=" + pjGallery.foreign_id,
			rebuildUrl: "index.php?controller=pjAdminGalleries&action=rebuildGallery&foreign_id=" + pjGallery.foreign_id,
			resizeUrl: "index.php?controller=pjAdminGalleries&action=resizeGallery&id={:id}&foreign_id=" + pjGallery.foreign_id,
			rotateUrl: "index.php?controller=pjAdminGalleries&action=rotateGallery",
			sortUrl: "index.php?controller=pjAdminGalleries&action=sortGallery",
			updateUrl: "index.php?controller=pjAdminGalleries&action=updateGallery",
			uploadUrl: "index.php?controller=pjAdminGalleries&action=uploadGallery&foreign_id=" + pjGallery.foreign_id,
			watermarkUrl: "index.php?controller=pjAdminGalleries&action=watermarkGallery&foreign_id=" + pjGallery.foreign_id
		});
		