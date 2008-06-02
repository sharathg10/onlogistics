function onchangeProduct(){
 if ($('monForm').elements['monSelectMvtType'].selectedIndex != 0) { // document.form.elements['monSelectMvtType'].selectedIndex
   $('monForm').submit();
   }
 }

function onchangeMvtType(){
 if ($('monForm').elements['monSelectProduct'].selectedIndex != 0) { // document.form.elements['monSelectProduct'].selectedIndex
   $('monForm').submit();
   }
 }
