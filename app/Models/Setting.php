<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'shop_name',
        'shop_address',
        'shop_phone',
        'shop_email',
        'invoice_logo',
        'invoice_signature',
    ];

    /**
     * Configuration en ligne unique (logo/signature de facture, coordonnées de la boutique) —
     * un seul enregistrement pour tout le site plutôt qu'un système de clé/valeur générique,
     * puisque ces quelques champs ne varient pas dans le temps.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['shop_name' => 'KhalilShop']);
    }

    /**
     * Disque de stockage des images produit/facture : Cloudinary dès que ses identifiants sont
     * renseignés dans .env (CLOUDINARY_URL), stockage local sur le disque `public` en attendant
     * (section 49 du cahier des charges).
     */
    public static function mediaDisk(): string
    {
        $url = config('cloudinary.cloud_url');

        return $url && $url !== 'cloudinary://' ? 'cloudinary' : 'public';
    }

    /**
     * URL affichable dans un navigateur (aperçu admin) pour l'un des champs image.
     */
    public function mediaUrl(string $field): ?string
    {
        if (! $this->$field) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk(self::mediaDisk())->url($this->$field);
    }

    /**
     * Image encodée en base64 pour la facture PDF : DomPDF n'a pas besoin d'aller chercher le
     * fichier à distance (Cloudinary) ni de connaître un chemin disque local, ce qui fonctionne
     * quel que soit le disque de stockage actif.
     */
    public function mediaDataUri(string $field): ?string
    {
        if (! $this->$field) {
            return null;
        }

        try {
            $disk = \Illuminate\Support\Facades\Storage::disk(self::mediaDisk());
            $contents = $disk->get($this->$field);
            $mime = $disk->mimeType($this->$field) ?: 'image/png';

            return 'data:'.$mime.';base64,'.base64_encode($contents);
        } catch (\Throwable) {
            return null;
        }
    }
}
