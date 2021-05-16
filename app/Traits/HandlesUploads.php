<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesUploads {

    public function getS3TemporaryUrl($path, $expires = "+20 seconds",$filename = null) {
        if (is_null($path)) return null;

        if (config('app.env') == "testing") {
            return "https://testing.s3.url.com";
        }

        if ($filename == null){
            $filename = array_last(explode("/", $path));
        }

        //S3 cannot add non-ascii characters in response headers
        if (!mb_check_encoding($filename, 'ASCII')) {
            $filename = uniqid();
        }

        /** @var \Aws\S3\S3Client $client */
        $client = Storage::getDriver()->getAdapter()->getClient();

        $command = $client->getCommand('GetObject', [
          'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
          'Bucket'                     => config('filesystems.disks.s3.bucket'),
          'Key'                        => $path,
        ]);

        $request = $client->createPresignedRequest($command, $expires);

        return (string)$request->getUri();
    }

    public function getUrlAttribute(){
        return Storage::url($this->path);
        return $this->getS3TemporaryUrl($this->path,"+20 seconds",$this->filename);
    }

    protected function uploadForEntity($entity, $image){
        $payload = $this->constructPayload($entity,$image);
        Storage::put($payload['path'],file_get_contents($image));
        return $entity->images()->create($payload);
    }

    private function constructPayload($entity,$image){
        $extension = $image->getClientOriginalExtension();
        $filename  = Str::random(10).'_'.time().'.'.$extension;
        $path = $entity->storage_prefix.'/'.$filename;
        $filename = $image->getClientOriginalName();
        return [
          'title' => substr($filename, 0, strrpos($filename, '.')),
          'path' => $path,
          'filename' => $filename,
          'extension' => $extension,
          'mime_type' => $image->getClientMimeType(),
          'size_in_bytes' => $image->getSize(),
        ];
    }
}