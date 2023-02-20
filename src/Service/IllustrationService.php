<?php  

// namespace App\Service;

// use Exception;
// use Symfony\Component\HttpFoundation\File\UploadedFile;
// use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

// class IllustrationService
// {
//     //aller chercher dans services.yaml
//     private $params;

//     public function __construct(ParameterBagInterface $params)
//     {
//         $this->params = $params;
//     }

//     public function add(UploadedFile $illustration, ?string $folder ='', ?int $width = 250, ?int $height = 250)
//     {
//         //on donne un nouveau nom à l'image
//         $fichier = md5(uniqid(rand(), true)) . '.webp';
        
//         //on récupère infos de l'image
//         $illustration_infos = getimagesize($illustration);

//         if($illustration_infos === false){
//             throw new Exception('Format d\'image incorrect');
//         }

//         //on vérifie le format de l'image
//         switch($illustration_infos['mime']){
//             case 'image/png':
//                 $illustration_source = imagecreatefrompng($illustration);
//                 break;
//             case 'image/jpeg':
//                 $illustration_source = imagecreatefromjpeg($illustration);
//                 break;
//             case 'image/webp':
//                 $illustration_source = imagecreatefromwebp($illustration);
//                 break;
//             default: 
//                 throw new Exception('Format d\'image incorrect');
//         }

//         //on recadre l'image
//         //on récupère les dimensions
//         $imageWidth = $illustration_infos[0];
//         $imageHeight = $illustration_infos[1];

//         //on vérifie l'orientation de l'image
//         switch ($imageWidth <=> $imageHeight){
//             case -1: //portrait
//                 $squareSize = $imageWidth;
//                 $src_x = 0;
//                 $src_y = ($imageHeight - $squareSize) / 2;
//                 break;
//             case 0: //carré
//                 $squareSize = $imageWidth;
//                 $src_x = 0;
//                 $src_y = 0;
//                 break;
//             case 1: //paysage
//                 $squareSize = $imageHeight;
//                 $src_y = ($imageWidth - $squareSize) / 2;
//                 $src_x = 0;
//                 break;

//         }

//         //on crée une vouvelle image "vierge"
//         $resized_illustration = imagecreatetruecolor($width, $height);
//         imagecopyresampled($resized_illustration, $illustration_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

//         $path = $this->params->get('images_directory') . $folder;

//         //on crée le dossier de destination s'il n'existe pas
//         if(!file_exists($path . '/mini/')){
//             mkdir($path . '/mini/', 0755, true);
//         }

//         //on stocke l'image recadrée
//         imagewebp($resized_illustration, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

//         $illustration->move($path . '/', $fichier);

//         return $fichier;

//     }

//     public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250)
//     {
//         if($fichier !== 'default.webp'){
//             $success = false;
//             $path = $this->params->get('images_directory') . $folder;

//             $miniature = $path . '/mini/'. $width . 'x' . $height . '-' . $fichier;

//             if(file_exists($miniature)){
//                 unlink($miniature);
//                 $success = true;
//             }

//             $original = $path . '/' . $fichier;

//             if(file_exists($original)){
//                 unlink($miniature);
//                 $success = true;
//             }
//             return $success;
//         }
//         return false;
//     }

// }