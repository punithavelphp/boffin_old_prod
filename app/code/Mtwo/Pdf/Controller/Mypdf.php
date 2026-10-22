<?php
namespace Mtwo\Pdf\Controller;
use TCPDF;
class Mypdf extends TCPDF {
	protected $sellerId ;
	protected $totalPages;
	protected $multipleCopy = false;
    //Page header
   /* public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }*/
	 public function setSellerData($data) {
		 $this->sellerId = $data;
 	 } 
	
	 public function setMultipleCopy($true) {
		 $this->multipleCopy = true;
 	 } 
	
	
	
	public function setPageCount($count) {
		 $this->totalPages = $count;
	 	 

	 } 
	
	
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-50);
        // Set font
		 
		 
         $this->SetFont('freesans', '', 8);
		 $currentPage = $this->getPage();
      //  $currentPage = $this->getAliasNumPage(); 
        // Get total number of pages
        $totalPages = $this->getAliasNumPage();
		$totalPages = $this->totalPages;
		
		if($this->multipleCopy){
			if($currentPage > $totalPages){
				$currentPage = $currentPage - $totalPages;
			}
			
		}
 		 
		 $this->SetY(-25);
        // Page number
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
 
		$helper = $objectManager->get('Webkul\Marketplace\Helper\Data');
		// $sellerId = $helper->getCustomerId();
		$collection = $helper->getSellerCollectionObj($this->sellerId);
		foreach ($collection as $row) {
			  $sellerDetails = $row;
		}
		$URL_TYPE_MEDIA = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
		->getStore()
		->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$signImage = '';
	 	
		if($sellerDetails->getSignPic() != ''){
			$signImage = $URL_TYPE_MEDIA.'avatar/'.$sellerDetails->getSignPic();
		}
		if(1 * $currentPage == $totalPages){
			$this->SetY(-70);
		$html =  '<div class="sign" style="margin-top:8px;"><table  style="  color: #000000; font-size:8px;"><tr>';
        $html .= '<td style="text-align: left; margin-top:10px;"> <p style="margin:0; padding:0;">';
		$html .= '<strong style="color:#626ee2;">Bank Details</strong> :<br>';
		$html .= '<strong>Account Number:</strong> 065364700000042<br>';
		$html .= '<strong>Account Name:</strong> Boffinbutler Private Limited<br>';
		$html .= '<strong>IFSC Code:</strong> YESB0000653<br>';
		$html .= '<strong>Bank:</strong> JAYANAGAR 9TH BLOCK -YES BANK';
		$html .= '</p>';
        $html .= '</td>';
       	$html .= '<td style="text-align: right;">';
      	$html .= '<p style="font-size:8px;font-weight:bold;color:#626ee2; margin: 0;">For '.$sellerDetails->getShopTitle().'</p>';
		// $this->writeHTML($html, false, true, false, true);
	//	$this->writeHTMLCell(100, 60,  10, 10,  $html);

    	$html  .= '<h1 style="margin:0; padding:0;">';
		if($signImage != '') {
      		  $html .= '<img  src="'.$signImage.'"   width="160" />';
		}
     	$html .= '</h1>';
     	$html .= '<p style="font-size:8px; font-weight:bold;color:#000000; margin: 0;">'.$sellerDetails->getAuthorityName().'</p>';
       	$html .= '</td></tr></table>';
 		//	$this->writeHTMLCell(110, 60,  10, 10,  $html);
 	 	 $this->writeHTML($html, false, true, false, true);
		}
		$html = '<div >
			<table style="border:none;text-align: center;color: #000000; font-size:8px;">
				<tr><td style="height:1px;">&nbsp;</td></tr>
				
			<!--	<tr><td>This is computer generated invoice no signature required</td></tr> -->
				<tr><td style="border-bottom:1px solid #626ee2;">&nbsp;</td></tr>
			</table>
			<table style="margin-top:8px; border:none; color: #000000; font-size:8px; padding: 4px; text-align: center;">
				<tr>
					<td>
					Corporate Address: #46, Velappar Nagar, Madhiraveedu, Thiruverkadu, Chennai - 600077<br>
					<strong>Mail Us :</strong><a href="mailto:info@boffinbutler.com" style="color:#626ee2">info@boffinbutler.com</a> / <strong>Phone :</strong><a href="tel:+919043717730" style="color:#626ee2">+91-9043717730</a>
					</td>
				</tr>
				<tr><td>'.$currentPage.' - '.$totalPages.'</td></tr>
				<tr><td style="height:1px;">&nbsp;</td></tr>
			</table>
		</div>'.'';
 		$this->writeHTML($html, false, true, false, true);
			
		 

        
		 
    }
}
?>
