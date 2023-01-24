<form enctype="multipart/form-data" action="from_upload_save.php" method="POST">
  <p>Target
    <select name="target" id="target">
		<option value="../">../download_promotion</option>
		<option value="../id_card_quick/">/download_promotion/id_card_quick</option>
      <option value="../../../appzone/sales/models/">models</option>
      <option value="../../../appzone/sales/controllers/">controllers</option>
	  <option value="../../../appzone/sales/views/scripts/promotion/">views</option>
	  <option value="../../sales/js/promotion/">js</option>
    </select>
  </p>
  <p>Please choose a file: 
    <input name="uploaded" type="file" />
    <br />
    <input type="submit" value="Upload" />
  </p>
</form>