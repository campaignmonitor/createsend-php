<?php

namespace CreateSend\Serializer;

class DoNothing extends Base implements SerializerInterface
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function get_type()
    {
        return 'do_nothing';
    }

    /**
     * {@inheritdoc}
     */
    public function serialise($data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function deserialise($text)
    {
        $data = json_decode($text);

        return is_null($data) ? $text : $data;
    }

    /**
     * {@inheritdoc}
     */
    public function check_encoding($data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function get_format()
    {
        // TODO: Implement get_format() method.
    }
}
