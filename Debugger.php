# colour-formatted print_r shortcut

if ( ! function_exists('prd')){ 
  function prd($object,$die=TRUE){
  
    # insert span tags
    $output = '<span class="die_value">'.$output;    
    $output = str_replace('[', '<span class="die_key">[', print_r($object,TRUE));
    $output = str_replace(']', ']</span>', $output);    
    $output = str_replace('=> ', '=> <span class="die_value">', $output);
      
    # temporarily swap these paterns 
    $output = str_replace(")\n\n", ")#br##br#", $output);
    $output = str_replace(")\n", ")#br#", $output);
    $output = str_replace("(\n", "(#br#", $output);    	
    	
    # close spans at remaining line breaks
    $output = str_replace("\n", "</span>\n", $output);

    # revert temporary swaps
    $output = str_replace(")#br##br#", ")\n\n", $output);
    $output = str_replace(")#br#", ")\n", $output);
    $output = str_replace("(#br#", "(\n", $output);
    	
    echo '<style type="text/css">#die_object { font-size: 11px; padding: 10px; background: #eee; font-family: monospace; white-space: pre;} .die_key { color: #e00;} .die_value { color: #00e;}</style>';
    
    if($die)
      die('<div id="die_object">'.$output.'</div>');      
    else
      echo('<div id="die_object">'.$output.'</div>'); 
  }
}
