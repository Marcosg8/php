<?php
$arrl = array(
1 => "3000",
2 => "4000",
);
$arr2 = array(
1 => 3000,
2 => 4000,
);
$arr3 = array(
2 => "4000",
1 => "3000",
);
if($arrl == $arr2){
echo "arrl y arr2 son iguales <br>";
}else{
echo "arrl y arr2 no son iguales <br>";
}
if($arrl == $arr3){
echo "arrl y arr3   son iguales <br>";
}else{
echo "arrl y arr3   no son iguales <br>";
}
if($arrl === $arr2){
echo "arrl y arr2   son iguales <br>";
}else{
echo "arrl y arr2   no son iguales <br>";
}
if($arrl === $arr3){
echo "arrl y arr3   son iguales <br>";
}else{
echo "arrl y arr3   no son iguales <br>";
}