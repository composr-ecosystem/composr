<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    code_quality
 */

function get_tests()
{
    $tests = array();

    // This is a good lexer test
    $tests[0] = <<<EOF
        function blah()  // test
        {
            \$testing="my \"lexer\"\t".'forever '.0.'!'.0.3.'!'.010.'!'.0xa3; /* woo */
            \$a=<<<END
Hi \$a
END;
        }
EOF;

    // This is a good parser test for functions
    $tests[1] = <<<END
function test(\$a,\$b)
{
    return 4;
}

test(1,2);

\$a='test';
\$a(1,2);
END;

    // This is a good test for loops
    $tests[2] = <<<END
foreach (array(1,2) as \$a=>\$b)
{
    while (true)
    {
        break;
    }

    for (\$i=0;\$i<10;\$i++)
    {
        continue;
    }

    do
    {
        // Pointless and unnecessary comment
    }
    while (false);
}

END;

    // This is a good test for objects
    $tests[3] = <<<END
class foobar
{
    var \$i;

    function __construct(\$a)
    {
        echo \$a;
    }
}

abstract class baz
{
    public \$x;
    private \$y;
    protected \$z;
    abstract public function p();
    abstract private function q();
    abstract protected function r();
}

\$a=new foobar(3);
\$b=clone \$a;
END;

    // This is a good test for conditionality
    $tests[4] = <<<END
if (true) \$a=1; elseif (true) \$a=1; else \$a=2;

switch (1)
{
    case 1:
        break;

    default:
}
END;

    // This is a good test for various things
    $tests[5] = <<<END
global \$A;
\$b=array(1=>3,2=>3);
echo \$A[\$b[1]];
\$c='foo';
echo \$c[0];
list(\$a1,\$b1,\$c1)=\$A;
\$A=array(-1,2,3);
\$c=!true;
\$d=\$c?1:0;
END;

    $tests[6] = <<<END
class foo
{
    function bar(\$meh,\$foo23bar,&\$var) { /*woo!*/

        if (((23-4)*5) && (\$meh==="\"'bootstrap&bill'\""))
        {
            return \$var;
        }
        elseif (true) {
            echo "/"."\\\\"."'\'\'\"".5.134.M_PI;
        }
    }

    var \$zipedeedoodaa;
}
END;

    $tests[7] = <<<END
if (\$pos===false) \$pos=strlen(\$_SERVER['SCRIPT_NAME']); else \$pos--;
END;

    $tests[8] = <<<END
// <global type conflict>

function foo()
{
    global \$A;
    \$A=1;
}

function bar()
{
    global \$A;
    \$A='1';
}
END;

    $tests[9] = <<<END
// <parameter type conflict>

function foo()
{
    \$a=1;
    \$a='1';
}
END;

    $tests[10] = <<<END
// <bad return>

function intval(\$a)
{
    return '';
}
END;

    $tests[11] = <<<END
// <missing return>

function intval(\$a)
{
}
END;

    $tests[12] = <<<END
// <bad arity (low)>

function foo()
{
    chmod('3');
}
END;

    $tests[13] = <<<END
// <bad arity (high)>

function foo()
{
    chmod('3','3','3');
}
END;

    $tests[14] = <<<END
// <bad parameters>

function foo()
{
    chmod(3,3);
}
END;

    $tests[15] = <<<END
// <local type conflict>

\$a=1;
\$a='1';
END;

    $tests[16] = <<<END
// <echo typing error>

echo 3;
END;

    $tests[17] = <<<END
// <switch inconsistency>

switch(3)
{
    case '4':
        break;
}
END;

    $tests[18] = <<<END
// <conditional typing error>

if ('a')
{
    echo 'doh';
}
END;

    $tests[19] = <<<END
// <foreach typing error>

foreach (3 as \$a)
{
    echo 'doh';
}
END;

    $tests[20] = <<<END
// <test of type checker feed through>

if (true)
{
    while (true)
    {
        foreach (array() as \$a)
        {
            do
            {
                    if (true)
                    {
                    } else
                    {
                            echo 3;
                    }
            }
            while (true);
        }
    }
}
END;

    $tests[21] = <<<END
// <continue typing error>

continue 'a';
END;

    $tests[22] = <<<END
// <increment typing error>

\$a='';
\$a++;
END;

    $tests[23] = <<<END
// <illegal reference passing>

\$a='';
intval(&\$a);
END;

    $tests[24] = <<<END
// <illegal special assignment>

\$a='';
\$a+=1;
END;

    $tests[25] = <<<END
// <illegal special assignment #2>

\$a=1;
\$a+='';
END;

    $tests[26] = <<<END
// <ternary if error #1>

\$a=''?1:2;
END;

    $tests[27] = <<<END
// <ternary if error #2>

\$a=true?'':2;
END;

    $tests[28] = <<<END
// <boolean logic error>

\$a=1 && 2;
END;

    $tests[29] = <<<END
// <conc error>

\$a='a'.2;
END;

    // This one will fail to parse
    $tests[30] = <<<END
// <illegal embedded assignment>

if (\$a='x' && true)
{
    echo 'whatever';
}
END;

    $tests[31] = <<<END
// <illegal negation>

\$a=-'a';
END;

    $tests[32] = <<<END
// <unreachable code>

function foo()
{
    return;
    echo 'unreachable';
}
END;

    $tests[33] = <<<END
// <bad continue depth>

continue;
END;

    $tests[34] = <<<END
// <usage of globals before globalisation>

\$A=1;
global \$A;
END;

    $tests[35] = <<<END
// <unused variable>

\$a=1;
END;

    $tests[36] = <<<END
// <bad construction>

\$a=new Tempcode(1);
unset(\$a);
END;

    $tests[37] = <<<END
// <array append mistake>

\$a=1;
\$a[]=1;
END;

    // This one will fail to parse
    $tests[39] = <<<END
// <$$>

\$\$foo=1;
END;

    // This one will fail to parse
    $tests[40] = <<<END
// <$$>

\$object->\$foo=1;
END;

    $tests[41] = <<<END
// <Unused variables>

\$foo=1;
\$foo=2;
END;

    $tests[42] = <<<END
// <Bad layout>

{
 }
END;

    $tests[43] = <<<END
// <Double dereferencing>

\$foo->bar->foo();
END;

    $tests[44] = <<<END
// <Complex variable referencing>

function x(\$a)
{
    \$a[1]=1;
}
END;

    $tests[45] = <<<END
// <! would bind to 0>

if (!fopen('foo','w')) exit();
END;

    $tests[46] = <<<END
// <string variable referencing>
\$a='';
echo "\$a";
END;

    $tests[47] = <<<END
// <deep variable referencing>
\$a='';
\$b=array();
echo \$b[''][''][''][''][\$a];
END;

    $tests[48] = <<<END
// <correct boolean interpretation>
\$a=false;
if (!\$a) exit();
END;

    $tests[49] = <<<END
// <deep variable referencing 2>
\$a='';
\$b=new foo();
echo \$b[\$a]->bar();
END;

    $tests[50] = <<<END
// <nice error offsets>
function fpassthru(\$a)
{
    unset(\$a);

    return false;
    return true;
}
END;

    $tests[51] = <<<END
\$a=array();
\$a[]='bar';
END;

    $tests[52] = <<<END
if (preg_match('a','a')) exit();
END;

    $tests[53] = <<<END
\$bool=\$whatever==null;
END;

    $tests[54] = <<<END
\$whatever_1=strpos('a',\$whatever_2);
END;

    $tests[55] = <<<END
\$whatever=array('a'=>1,'a'=>1);
END;

    $tests[56] = <<<END
preg_match_all('','',array());
END;

    $tests[57] = <<<END
set_time_limit(0);
END;

    $tests[58] = <<<END
\$test='';
\$a='';
\$test.=&\$a;
END;

    // Tests chaining method calls and associated jiggery-pokery
    $tests[59] = <<<END
class A
{
    public \$a;
    public function b()
    {
        return \$this;
    }
}
\$irrelevant_variable_name=new A();
\$irrelevant_variable_name->intermediate_function()->critical_type_part();
\$irrelevant_variable_name->b()->x();
\$irrelevant_variable_name->b()->b()->b()->b()->a=5;
END;

    // Tests interfaces
    $tests[60] = <<<END
interface A
{
    public \$a=10;
    public function foo(\$bar);
}
interface B extends A
{
    public \$b="abc";
    public function bar(\$foo);
}
interface C
{
    public \$c=array();
    public function baz();
}
interface D extends A, C
{
    public function foobar();
}
class Foo implements A
{
    public function __construct(\$bar)
    {
        // Do nothing
    }
}
END;

    // Tests type hinting
    $tests[61] = <<<END
class A
{
    public function foo(A \$a, A \$b=null, A &\$c, array \$d, array \$e=null, array &\$f, array &\$g, \$h, \$i=5)
    {
    }
}
END;

    $tests[62] = <<<END
try
{
    \$composr->controller('site_pageload')->do_page_script();
}
catch (CMS_Exception \$e)
{
    \$e->terminate_with_exception();
}
END;

    $tests[63] = <<<END
\$composr->controller('site_pageload')->do_page_script();
END;

    // Tests type redefining
    $tests[64] = <<<END
if (false)
{
    class A
    {
        function test()
        {
            \$a=\$b;
            if (false)
            {
                    function bar()
                    {
                            \$c=\$d;
                    }
            }
        }
    }
}
END;

    $tests[65] = <<<END
if (\$a->b) exit();
END;

    $tests[66] = <<<END
\$a=@\$b;
END;

    $tests[67] = <<<END
foreach (array() as \$a)
{
    foreach (array() as \$a)
    {
    }
}
END;

    return $tests;
}
