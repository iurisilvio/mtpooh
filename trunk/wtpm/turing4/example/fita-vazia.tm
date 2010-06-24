/ máquina para testar a impressão de caracteres vazios na fita.

/ basicamente tem 6 estados que andam para a direita, e depos dois para voltar tudo até travar.
/ bonus! rejeita qualquer entrada que não tenha os primeiros 7 caracteres em branco.
8 1
a
b
c
d
e
f
g
L

/ estado final
L

a {
  /lê blank, incrementa uma posição e um estado.
  #:#,b,R;
}
b{
  #:#,c,R;
}
c{
  #:#,d,R;
}
d{
  #:#,e,R;
}
e{
  #:#,f,R;
}
f{
  #:#,g,R;
}
g {
  /substitui o sétimo por !
  #:!,L,L;
}
L {
  /volta até travar.
  #:#,L,L;
}
