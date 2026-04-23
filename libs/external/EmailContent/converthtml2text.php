<?php
class ConvertHTML2Text
{	

	private $lineWidth = 70;
	private $bracketFront = "(";
	private $bracketBack = ")";			
	private $topDoubleLineDivider = "=====";		
	private $singleLineDivider = "\n------------\n";
	private $placeHolderCharacter = ".";

	const FORMAT_ARRAY = [
		'txt_hidden' =>
			[
				'hidden' => 'true',
			],
		'txt_linebreak' =>
			[
				'linebreak_front' => 1,
			],
		'txt_componentqty' =>
			[
				'indent' => 5,
				'linebreak_front' => 1,
			],
		'txt_subcomponentqty' =>
			[
				'indent' => 10,
				'linebreak_front' => 1,
			],
		'txt_billingaddress' =>
			[
				'linebreak_front' => 2,
			],
		'txt_shippingaddress' =>
			[
				'linebreak_front' => 2,
			],
		'txt_total-heading' =>
			[
				'linebreak_front' => 1,
			],
		'txt_itemcomponentinfo' =>
			[
				'indent' => 5,
			],
		'txt_itemcomponentpriceinfo' =>
			[
				'indent' => 5,
			],
		'txt_itemsubcomponentinfo' =>
			[
				'indent' => 10,
			],
		'txt_itemsubcomponentpriceinfo' =>
			[
				'indent' => 10,
			],
		'txt_subComponentMetaData' =>
			[
				'indent' => 5,
			],
		'txt_subcomp_orderfooterlinetotal' =>
			[
				'indent' => 10,
				'linebreak_front' => 1,
			],
		'txt_subsection' =>
			[
				'indent' => 10,
				'linebreak_front' => 1,
			],
		'txt_ordermetadataname' =>
			[
				'starsWrap' => 'true',
				'indent' => 5,
				'linebreak_front' => 2,
			],
		'txt_metadataname' =>
			[
				'starsWrap' => 'true',
				'indent' => 5,
				'linebreak_front' => 1,
			],
		'txt_metadatadescription' =>
			[
				'bracketsWrap' => 'true',
				'indent' => 5,
				'linebreak_front' => '1',
			],
		'txt_metadatavalue' =>
			[
				'linebreaksafe' => true,
				'indent' => 5,
				'linebreak_front' => 1,
			],
		'txt_ProjectName' =>
			[
				'linebreak_front' => 1,
			],
		'txt_linetotal' =>
			[
				'linebreak_front' => 1,
			],
		'txt_paymentmethod' =>
			[
				'linebreak_front' => 2,
			],
		'txt_orderfooterlinetotal' =>
			[
				'indent' => 5,
				'linebreak_front' => 1,
			],
		'txt_orderfooterfinaltotal' =>
			[
				'linebreak_front' => 1,
			],
		'txt_includetaxtextorderfooterfinal' =>
			[
				'linebreak_front' => 1,
			],
		'txt_shippinglinetotal' =>
			[
				'linebreak_front' => 1,
			],
		'txt_includetaxtext' =>
			[
				'linebreak_front' => 1,
			],
		'txt_orderfooterincludetaxtext' =>
			[
				'indent' => 5,
				'linebreak_front' => 1,
			],
		'txt_sub_orderfooterincludetaxtext' =>
			[
				'indent' => 10,
				'linebreak_front' => 1,
			],
		'txt_singleLineDivider' =>
			[
				'linebreak_front' => 1,
				'singleLineDivider' => 'true',
			],
		'txt_sectionHeader' =>
			[
				'title_doubleline' => 'true',
			],
		'txt_asset' =>
			[
				'indent' => 5,
				'linebreak_front' => 2,
			],
		'txt_qty' =>
			[
				'qty' => 'true',
			],
		'txt_subqty' =>
			[
				'qty' => 'true',
			],
		'txt_price' =>
			[
				'rightColumn' => 'true',
			],
		'txt_photoprints_qty' =>
			[
				'indent' => 5,
			],
		'txt_photoprints_price' =>
			[
				'indent' => 5,
			],
		'txt_product_qty' =>
			[
				'indent' => 5,
			],
	];
			
	public function getStylingData($className)
	{
		if (isset(self::FORMAT_ARRAY[$className]))
		{
			return self::FORMAT_ARRAY[$className];
		}
		else
		{
			return false;		
		}
	}
	
	
	public function Convert($data)
	{										
		// Remove unneccessarry while spaces between tags.
		$data = preg_replace('~>\s+<~', '><',$data);
		
		// Remove all HTML tag except some allowed tags
		$text = strip_tags($data,"<table></table><tr></tr><td></td><br /><br><br/><span></span>");
		
		// Convert every courier character to \n
		$text = self::mb_str_replace("\r","\n",$text);
		$text = self::mb_str_replace("<br />","\n",$text);
		$text = self::mb_str_replace("<br>","\n",$text);
		$text = self::mb_str_replace("<br/>","\n",$text);
		$text = self::mb_str_replace('&nbsp;', ' ', $text);			
			
		$encoding = mb_detect_encoding($text);
		$dom = new DOMDocument('1.0',$encoding);
		$text = mb_convert_encoding($text, 'HTML-ENTITIES', $encoding);
		$dom->loadHTML($text);
		
		self::processSpans($dom, '');				
		
		// saveHTML each time updating an object. Otherwise unexpected result would happen
		$text = $dom->saveHTML();		
		$encoding = mb_detect_encoding($text);
		
		$dom = new DOMDocument('1.0',$encoding);
		$dom->loadHTML($text);
								
		self::processTables($dom, '');
				
		$htmlContent = $dom->saveHTML();
		$textContent = strip_tags($htmlContent);
		$textContent = self::mb_str_replace('@nbsp;', ' ', $textContent);	
		$textContent = self::mb_str_replace('@linebreak', "\n", $textContent);			
		$textContent = mb_convert_encoding($textContent, 'UTF-8', 'HTML-ENTITIES');
						
		$lines = explode("\n",$textContent); 
		
		$finalString = '';		
		foreach ($lines as $line)
		{
			if(mb_strpos($line, "@RIGHTCOLUMN@", 0, 'UTF-8') !== false)
			{
				$stringElements = explode("@RIGHTCOLUMN@", $line);				
				$firstColumn = self::mb_str_pad($stringElements[0], $this->lineWidth, $this->placeHolderCharacter,STR_PAD_RIGHT);									
				$secondColumn = $stringElements[1];
				
				$line = $firstColumn.$secondColumn;
			}

			$finalString .= $line."\n";
		}
		return $finalString;		
	}
	
		
	public function processSpans($dom = '', $spans = '')
	{		 				
		// Select parent spans
		if($spans == '')
		{			
			// Select spans and process their content according to their class
			$spans = $dom->getElementsByTagName('span');		
		}
				
		//loop around spans					
		foreach ($spans as $span)
		{        																								
			// for nested spans
			if($span->hasChildNodes())
			{				
				$childNodes = $span->childNodes;				
				$this->processSpans($dom, $childNodes);		
			}

			$spanClass = self::getTagAttributes($span, 'class');										
			// Only process classes starting with txt_
			if (mb_substr($spanClass, 0, 4, 'UTF-8') === 'txt_')
			{				
				$proccessedClass = self::getTagAttributes($span, 'proccessed');
						
				$stylingData = self::getStylingData($spanClass);
				if($stylingData != false && $proccessedClass != 1 )
				{					
					// loop around styling element. 
					foreach ($stylingData as $key => $value)
					{
						self::processDomElementsContent($span, $key, $value);
					}	
				}
			}				
		}																				
	}
	
	
	public function processTables($dom)
	{		
		// Move quantity to the front.
		$tables = $dom->getElementsByTagName('table');							
		foreach($tables as $table)
		{
			$rows = $table->getElementsByTagName('tr');			
			foreach ($rows as $row)	
			{
				$columnIndex = 0;	
				$columns = $row->getElementsByTagName('td');
				{
					foreach ($columns as $column)
					{
						if($columnIndex == 0)
						{
							$firstNode = $column;
						}
						
						$className = self::getTagAttributes($column, 'class');	
						$processed = self::getTagAttributes($column, 'proccessed');	

						if (($className == 'quantity' || $className == 'txt_componentqty' || $className == 'txt_subcomponentqty' ) && $processed != 1)
						{
							$stylingData = self::getStylingData($className);
							if($stylingData != false && $processed != 1 )
							{					
								// loop around styling element. 
								foreach ($stylingData as $key => $value)
								{
									self::processDomElementsContent($column, $key, $value);									
								}	
							}

							$newNode = $column->cloneNode(true);
							$newNode->setAttribute('proccessed', '1');							
							$firstNode->parentNode->insertBefore($newNode, $firstNode);
							$column->parentNode->removeChild($column);							
						}
		
						$columnIndex ++;
					}
				}
			}
		}	
		
		// Add linebreak at begining of each row
		$tables = $dom->getElementsByTagName('table');							
		foreach($tables as $table)
		{
			$rows = $table->getElementsByTagName('tr');			
			foreach ($rows as $row)	
			{
				$columnIndex = 0;	
				$columns = $row->getElementsByTagName('td');
				{
					foreach ($columns as $column)
					{
						$cellValue = self::mb_trim(htmlentities($column->nodeValue, ENT_QUOTES, 'UTF-8'));

						// to detect the first column & put a line break in
						if($columnIndex == 0)
						{															
							$column->nodeValue = "\n" . $cellValue;
						}
						else
						{
							$column->nodeValue = $cellValue;
						}
						$columnIndex ++;
					}
				}
			}	
		}
	}
	
	
	public function mb_trim($string)
	{
		$string = preg_replace( "/(^\s+)|(\s+$)/us", "", $string );
	   
		return $string;
	} 
	
	
	public function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT) 
	{
		$diff = strlen($input) - mb_strlen($input);

		return str_pad($input, $pad_length+$diff, $pad_string, $pad_type);
	}
	
	
	public function mb_str_replace($needle, $replacement, $haystack) 
	{
	   return implode($replacement, mb_split($needle, $haystack));
	}
	
	
	public function getTagAttributes($pTag, $pAttribute)
	{
		$result = '';
	
		if ($pTag && $pTag->hasAttributes())
		{
			$attribute = $pTag->getAttribute($pAttribute);	

			if($attribute !== null)
			{
				$result = (string) $attribute;
			}
		}
		return $result;
	}
	
	 
	function processDomElementsContent($node, $attribute, $attributeValue)
	{		
		$nodeValue = self::mb_trim($node->textContent);	
		
		// Process nodeValue based on its classes
		if ($attribute == 'title_doubleline'	&& 	$attributeValue == 'true' && $nodeValue != '')				
		{
			$nodeValue = "\n\n" . $this->topDoubleLineDivider . " " . $nodeValue . " " . $this->topDoubleLineDivider; 							
		}	
		else
		if ($attribute == 'linebreak_front' && $attributeValue > 0)
		{							
			$nodeValue = str_repeat("@linebreak",$attributeValue) . $nodeValue; 																					
		}	
		else
		if ($attribute == 'linebreak_back' && $attributeValue > 0)
		{							
			$nodeValue = $nodeValue . str_repeat("@linebreak", $attributeValue); 																					
		}	
		else
		if ($attribute == 'bracketsWrap' && $attributeValue == 'true' && $nodeValue != '' )
		{																																				
			$nodeValue = $this->bracketFront . $nodeValue . $this->bracketBack; 																			
		}	
		else
		if ($attribute == 'singleLineDivider' && $attributeValue == 'true')				
		{
			$nodeValue = $this->singleLineDivider; 							
		}	
		else
		if ($attribute == 'qty' && $attributeValue == 'true' && $nodeValue != '')				
		{
			// used @ instead of & because saveHTML will remove the spaces randomly
			$nodeValue = $nodeValue . '@nbsp;X@nbsp;'; 							
		}	
		else
		if ($attribute == 'starsWrap' && $attributeValue == 'true' && $nodeValue != '' )								
		{
			// used @ instead of & because saveHTML will remove the spaces randomly
			$nodeValue = '*' . $nodeValue . "*"; 	
		}	
		else
		if ($attribute == 'rightColumn' && $attributeValue == 'true' && $nodeValue != '' )										
		{
			$nodeValue = "@RIGHTCOLUMN@" . $nodeValue;						
		}	
		else
		if ($attribute == 'linebreaksafe' && $attributeValue == 'true' && $nodeValue != '')
		{
			$nodeValue = self::mb_str_replace("\n\n","@@TAOPIXLINEBREAK@@",$nodeValue);						
			$nodeValue = self::mb_str_replace("\n","@@TAOPIXLINEBREAK@@",$nodeValue);									
		}
		else
		if ($attribute == 'indent' && $attributeValue > 0)
		{
			// If the parent node is indentted then add its indent value to the current node. 
			$parentNode = $node->parentNode;				
			$parentClass = self::getTagAttributes($parentNode, 'class');
			$parentStylingData = self::getStylingData($parentClass);

			if(($parentStylingData !== false) && ($parentStylingData['indent'] != false))
			{
				$attributeValue = $attributeValue + $parentStylingData['indent'];
			}

			// if the content is multiple line then apply the indentation to all lines
			$lines = explode("@@TAOPIXLINEBREAK@@",$nodeValue);		
			$tempNodeValue = '';
			if (count($lines) > 0)
			{
				foreach ($lines as $line)
				{
					$tempNodeValue .= str_repeat("@nbsp;", $attributeValue) . $line."\r";				
				}			
			}
			else
			{
				$tempNodeValue .= str_repeat("@nbsp;", $attributeValue) . $nodeValue;				
			}

			$nodeValue = $tempNodeValue;				
		}	
		else
		if ($attribute == 'hidden' && $attributeValue == 'true')										
		{
			$nodeValue = '';
		}
		
		$node->nodeValue = $nodeValue;	
		
		// set an extra attribute "proccessed" = 1 to mark this span has been proccessed. 
		$node->setAttribute('proccessed', '1');	
	}		
}
?>