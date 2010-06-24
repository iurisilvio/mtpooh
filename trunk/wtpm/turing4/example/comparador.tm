/** @author Pooh
  */

/* Nova versão da TPM (Turing-Pooh Machine)
   Features novos:
     - Fita agora é semi infinita (antes tinha a acochambração de 
     que a fita era infinita para ambos os lados.
     a máquina trava quando está na primeira posição e tenta ir para
     a esquerda.
     - Agora você pode comentar a descrição da máquina que vc criar.
     Duas opções de comentários multi-linha (como esse), ou até o fim
     de linha começando com /
     - Os comentários podem aparecer em qualquer lugar, desde que não
     quebrem o nome de um estado no meio.
     - Ainda não aceita comentários encadeados!!
     - Nomes de estados podem conter alfa-numéricos e _+*&~!-
     - Há mensagens de erro.
     - Limitei o número de iterações da máquina em 50k para facilitar a
     versão web. (valeu panda!)
 */

16 /exemplo de comentário de uma linha
start
fix	 left	  pick
carry-a1 carry+a2 carry~b1
carry*b2 carry&c1
carry_c2 /* comentário no meio do nada */ match
!!true!! fail
set_next rewind   false

start /* aqui também pode comentar*/ /* inclusive vários seguidos*/ / gostou?
{
    0:^,fix,R;
    / whitespace nas transições é desprezado. 
    a : a , start, L
;b:b,start,R; /comentários no meio das transições também rola



 ! /* espero que esse caractere nunca apareça */ :
 / vai ser substituído por
 V , /* vou para o estado */ fix /* e ando */ , /* para a */ L /Esquerda!
/* e um ponto e vírgula para acabar.*/ ;
}
fix {
      a:a,fix,R;
      b:b,fix,R;
      c:c,fix,R;
      0:^,left,L;
}
left {
     0:0,left,L;
     a:a,left,L;
     b:b,left,L;
     c:c,left,L;
     ^:^,pick,R;
     A:A,pick,R;
     B:B,pick,R;
     C:C,pick,R;
}
pick {
     0:0,!!true!!,R;
     ^:^,!!true!!,R;
     a:A,carry-a1,R;
     b:B,carry~b1,R;
     c:C,carry&c1,R;
}
carry-a1 {
	 a:a,carry-a1,R;
	 b:b,carry-a1,R;
	 c:c,carry-a1,R;
	 0:0,carry-a1,R;
	 ^:^,carry+a2,R;
}
carry~b1 {
	 a:a,carry~b1,R;
	 b:b,carry~b1,R;
	 c:c,carry~b1,R;
	 0:0,carry~b1,R;
	 ^:^,carry*b2,R;
}
carry&c1 {
	 a:a,carry&c1,R;
	 b:b,carry&c1,R;
	 c:c,carry&c1,R;
	 0:0,carry&c1,R;
	 ^:^,carry_c2,R;
}
carry+a2 {
	 A:A,carry+a2,R;
	 B:B,carry+a2,R;
	 C:C,carry+a2,R;
	 :,false,L;
	 b:b,fail,L;
	 c:c,fail,L;
	 a:A,match,L;
}
carry*b2 {
	 A:A,carry*b2,R;
	 B:B,carry*b2,R;
	 C:C,carry*b2,R;
	 :,false,L;
	 a:a,fail,L;
	 c:c,fail,L;
	 b:B,match,L;
}
carry_c2 {
	 A:A,carry_c2,R;
	 B:B,carry_c2,R;
	 C:C,carry_c2,R;
	 :,false,L;
	 b:b,fail,L;
	 a:a,fail,L;
	 c:C,match,L;
}
!!true!! {}
false {}
match {
      A:A,match,L;
      B:B,match,L;
      C:C,match,L;
      ^:^,left,L;
}
fail {
     A:a,fail,L;
     B:b,fail,L;
     C:c,fail,L;
     ^:0,set_next,R;
}
set_next {
	 a:^,rewind,L;
	 b:^,rewind,L;
	 c:^,rewind,L;
}
rewind {
       0:0,rewind,L;
       a:a,rewind,L;
       b:b,rewind,L;
       c:c,rewind,L;
       A:a,rewind,L;
       B:b,rewind,L;
       C:c,rewind,L;
       ^:^,pick,R;
}
depois de ler a última descrição de estado o programa caga, então dá para colocar comentários finais
sem as barrinhas indicativas...

obrigado pelo apoio, galera!
--
[]'s
Pufe.
