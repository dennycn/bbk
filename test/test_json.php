
<?php

test1();

# test 1
function test1()
{
    $program = '/usr/bin/python ../search/metasearch.py sogou mp3';
    print($program.'<br>');
    $str = exec($program);
    // TODO: json_decode error
    $json_str = json_decode($str, true);  //['data'];
    print("\n");
    $data = $json_str['data'];
    //print_r($json_str);
    $result_num = count ($data);
    printf("total=%d\n", $result_num);

}


# test 2
function test2()
{
    $str = file_get_contents('1.json');
    $json_str = json_decode($str, true);
    print('6');
    print_r($json_str);
    //var_dump($json_str->data);
    var_dump($json_str['data']);
}
