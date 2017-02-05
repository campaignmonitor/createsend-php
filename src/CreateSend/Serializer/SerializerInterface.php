<?php

namespace CreateSend\Serializer;

interface SerializerInterface
{
    /**
     * @return string
     */
    public function get_format();

    /**
     * @return string
     */
    public function get_type();

    /**
     * @param $data
     * @return mixed
     */
    public function serialise($data);

    /**
     * @param $text
     * @return mixed
     */
    public function deserialise($text);
}
