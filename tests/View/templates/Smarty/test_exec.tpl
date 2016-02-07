{assign var="var1" value="some value"}
exec: {exec controller="exec_test" action="inner" param1="a string" param2=42}
exec2: {exec controller="exec_test" action="inner_with_response" param1="hello"}
{$var1} {$assigned1} {$assigned2}