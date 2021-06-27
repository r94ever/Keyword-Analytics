<?php

namespace Qmas\KeywordAnalytics\Tests\Unit;

use Qmas\KeywordAnalytics\Helper;
use Qmas\KeywordAnalytics\Tests\TestCase;

class HelperTest extends TestCase
{
    public function test_strip_html_tags()
    {
        $str = '<body><a class="test">te < st> </a></body>';

        $str = Helper::stripHtmlTags($str);

        $this->assertEquals('te < st> ', $str);
    }

    public function test_unicode_to_ascii_vietnamese()
    {
        $str = Helper::unicodeToAscii('áàảãạ ÁÀẢÃẠ ẫầấẩậ ẤẦẨẪẬ ắằẳẵặ ẮẰẲẴẶ', false);
        $this->assertEquals('aaaaa AAAAA aaaaa AAAAA aaaaa AAAAA', $str);

        $str = Helper::unicodeToAscii('Đđ', false);
        $this->assertEquals('Đđ', $str);

        $str = Helper::unicodeToAscii('éèẻẽẹ ÉÈẺẼẸ ễệếềể ẾỀỂỄỆ', false);
        $this->assertEquals('eeeee EEEEE eeeee EEEEE', $str);

        $str = Helper::unicodeToAscii('íìỉĩị ÍÌỈĨỊ', false);
        $this->assertEquals('iiiii IIIII', $str);

        $str = Helper::unicodeToAscii('ốồổỗộ ớờởỡợ ỐỒỔỖỘ ỚỜỞÕỢ', false);
        $this->assertEquals('ooooo ooooo OOOOO OOOOO', $str);

        $str = Helper::unicodeToAscii('ứừửữự ỨỪỬỮỰ', false);
        $this->assertEquals('uuuuu UUUUU', $str);

        $str = Helper::unicodeToAscii('Chuyển tự tiếng Nga sang ký tự Latinh là một việc cần thiết để viết ký âm các tên hay các địa danh dưới dạng tiếng Nga sang dạng phiên âm trong các ngôn ngữ dùng ký tự Latinh, ví dụ như tiếng Việt. Việc chuyển tự còn cần thiết cho thao tác văn bản tiếng Nga trên máy tính nhưng lại không có bàn phím (JCUKEN) hoặc chương trình xử lý văn bản chuyên biệt để nhập ký tự Cyrill. Trong trường hợp này, họ có thể sử dụng một công cụ (phần mềm) cho phép họ có thể nhập phiên âm Latinh trên bàn phím của họ (QWERTY) rồi tự động chuyển đổi văn bản sang các ký tự Cyrill (chẳng hạn như phần mềm VietKey).', false);
        $this->assertEquals('Chuyen tu tieng Nga sang ky tu Latinh la mot viec can thiet đe viet ky am cac ten hay cac đia danh duoi dang tieng Nga sang dang phien am trong cac ngon ngu dung ky tu Latinh vi du nhu tieng Viet Viec chuyen tu con can thiet cho thao tac van ban tieng Nga tren may tinh nhung lai khong co ban phim JCUKEN hoac chuong trinh xu ly van ban chuyen biet đe nhap ky tu Cyrill Trong truong hop nay ho co the su dung mot cong cu phan mem cho phep ho co the nhap phien am Latinh tren ban phim cua ho QWERTY roi tu đong chuyen đoi van ban sang cac ky tu Cyrill chang han nhu phan mem VietKey', $str);
    }

    public function test_unicode_to_ascii_russian()
    {
        $str = Helper::unicodeToAscii('Бб Вв Гг Дд Ее Ёё Жж Зз Ии Йй Кк Лл† Мм Нн Оо Пп Рр Сс Тт Уу Фф Хх Цц Чч Шш Щщ Ъъ Ыы Ьь Юю Яя Іі', false);
        $this->assertEquals('Bb Vv Gg Dd Ee Ee Zz Zz Ii Jj Kk Ll Mm Nn Oo Pp Rr Ss Tt Uu Ff Hh Cc Cc Ss Ss ʺʺ Yy ʹʹ Uu Aa Ii', $str);

        $str = Helper::unicodeToAscii('Транслитерация русского текста латиницей, или романизация русского текста, транслитерация русского текста с кириллицы на латиницу — передача букв, слов, выражений и связанных текстов, записанных с помощью русского алфавита (кириллического), средствами латинского алфавита.', false);
        $this->assertEquals('Transliteracia russkogo teksta latinicej ili romanizacia russkogo teksta transliteracia russkogo teksta s kirillicy na latinicu  peredaca bukv slov vyrazenij i svazannyh tekstov zapisannyh s pomosʹu russkogo alfavita kirilliceskogo sredstvami latinskogo alfavita', $str);
    }

    public function test_unicode_to_ascii_japanese()
    {
        $str = Helper::unicodeToAscii('ロシア語のラテン文字表記法（ロシアごのラテンもじひょうきほう）では、ロシア語の表記に用いられるキリル文字をラテン文字へと翻字する方法について記述する。これはロシア人の名前やロシア関連事物を非キリル文字で記すために必要となる。');
        $this->assertEquals('roshia yunoraten wen zi biao ji fa roshiagonoratenmojihyoukihoudeharoshia yuno biao jini yongirarerukiriru wen ziworaten wen ziheto fan zisuru fang fanitsuite ji shusurukoreharoshia renno ming qianyaroshia guan lian shi wuwo feikiriru wen zide jisutameni bi yaotonaru', $str);
    }

    public function test_unicode_to_ascii_arabian()
    {
        $str = Helper::unicodeToAscii('نسخ الروسية هو كتابة الكلمات الروسية بغير الحروف الكيريلية.');
        $this->assertEquals('nskh alrwsyt hw ktabt alklmat alrwsyt bghyr alhrwf alkyrylyt', $str);
    }

    public function test_unicode_to_ascii_chinese()
    {
        $str = Helper::unicodeToAscii('俄语罗马化（俄語：Транслитерация русского алфавита латиницей、俄語拉丁化）是指把俄语从西里尔字母转写到拉丁字母这一过程。这种转写常用于把俄语人名和其他词语（比如Иван）转换为拉丁字母（Ivan）以便书写、印刷。俄语罗马化在没有俄语输入法或俄语输入困难的计算机使用者手上是非常重要的工具。尽管时至今天俄语的ЙЦУКЕН键盘早就普及了，但仅限于俄国境内。由于俄国国外的键盘基本都是QWERTY键盘，这使得不熟悉ЙЦУКЕН键盘布局的用户在输入西里尔字母的时候相当麻烦，因为键盘键帽上没有印刷西里尔字母供参考。配合一些软件，用户可以用他们熟悉的QWERTY键盘输入罗马化的俄语词汇，通过软件逆转换为西里尔字母。');
        $this->assertEquals('e yu luo ma hua e yutransliteracia russkogo alfavita latinicej e yu la ding hua shi zhi ba e yu cong xi li er zi mu zhuan xie dao la ding zi mu zhe yi guo chengzhe zhong zhuan xie chang yong yu ba e yu ren ming he qi ta ci yu bi ruivan zhuan huan wei la ding zi mu ivan yi bian shu xie yin shuae yu luo ma hua zai mei you e yu shu ru fa huo e yu shu ru kun nan de ji suan ji shi yong zhe shou shang shi fei chang zhong yao de gong jujin guan shi zhi jin tian e yu dejcuken jian pan zao jiu pu ji le dan jin xian yu e guo jing neiyou yu e guo guo wai de jian pan ji ben dou shiqwerty jian pan zhe shi de bu shu xijcuken jian pan bu ju de yong hu zai shu ru xi li er zi mu de shi hou xiang dang ma fan yin wei jian pan jian mao shang mei you yin shua xi li er zi mu gong can kaopei he yi xie ruan jian yong hu ke yi yong ta men shu xi deqwerty jian pan shu ru luo ma hua de e yu ci hui tong guo ruan jian ni zhuan huan wei xi li er zi mu', $str);
    }

    public function test_remove_html_tags_and_content()
    {
        $str = "<P><em>test</em></P>";
        $this->assertEquals('<P></P>', Helper::removeHtmlTagsAndContent($str, ['em']));
    }


}