<?php

namespace CreateSend\Serializer;

class NativeJson extends Base implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function get_format()
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function get_type()
    {
        return 'native';
    }

    /**
     * {@inheritdoc}
     */
    public function serialise($data)
    {
        if (is_null($data) || $data == '') {
            return '';
        }

        return json_encode($this->check_encoding($data));
    }

    /**
     * {@inheritdoc}
     */
    public function deserialise($text)
    {
        $data = json_decode($text);

        return $this->strip_surrounding_quotes(is_null($data) ? $text : $data);
    }

    /**
     * We've had sporadic reports of people getting ID's from create routes with the surrounding quotes present.
     * There is no case where these should be present. Just get rid of it.
     *
     * @param string $data
     * @return string
     */
    public function strip_surrounding_quotes($data)
    {
        if (is_string($data)) {
            return trim($data, '"');
        }

        return $data;
    }
}
