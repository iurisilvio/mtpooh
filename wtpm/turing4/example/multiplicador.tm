/*
  Seja bem vindo ao TPM-GG/WB+. Este programa simula uma máquina de turing
  e emite um diagrama de estados correspondente à entrada fornecida. A
  documentação da sintaxe de entrada é este arquivo.

  Esta máquina de exemplo é um multiplicador. Se a entrada for
  0010001 (equivalente a 2 * 3), a saída será 000000.

  Espaço é #.
*/

/* Números abaixo são:
   <num. estados total> <num. estados finais> <num. subrotinas> */
11 1 1
/* Estados */
q0 q1 q2 q3 q4 q5 q6 q7 q8 q9 qf / primeiro é estado inicial
/* Estados Finais (só pra diagrama)*/
qf
/* Subrotinas (só pra diagrama) */
copy 6 q2 q3 q4 q5 q6 q7

/* Exemplo de Input: 2 * 3 escreve-se 0010001 */

/ transições partindo de q0
q0 {
  0:#,q1,R; / lê 0, escreve B, vai pra q1, cabeça pra direita
  1:#,q9,R; / lê 1, escreve B, vai pra q9, cabeça pra direita
}

q1
{
  0:0,q1,R;
  1:1,q2,R;
}

q2
{
  0:X,q3,R;
  1:1,q7,L;
}

q3
{
  0:0,q3,R;
  1:1,q4,R;
}

q4
{
  0:0,q4,R;
  #:0,q5,L;
}

q5
{
  0:0,q5,L;
  1:1,q6,L;
}

q6
{
  0:0,q6,L;
  X:X,q2,R;
}

q7
{
  X:0,q7,L;
  1:1,q8,L;
}

q8
{
  0:0,q8,L;
  #:#,q0,R;
}

q9
{
  0:#,q9,R;
  1:#,qf,R;
}

qf
{
}
