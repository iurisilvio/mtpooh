#include "machine.h"

void tape::read(FILE* fin)
{
  for(int i=pos; ; ++i)
    {
      if (fscanf(fin, " %c", &vet[i])!=1)
	break;
      if (vet[i]=='?')
	{
	  vet[i]=' ';
	  break;
	}
    }
}
void tape::print()
{
  int begin, end, size;
  size=vet.size();
  for(begin=0; begin<size; ++begin)
    if (vet[begin]!=' ')
      break;
  for(end=size-1; end>=0; --end)
    if (vet[end]!=' ')
      break;
  if (pos<begin)
    printf("()");
  for(; begin<=end; ++begin)
    if (begin==pos)
      printf("(%c)", vet[begin]);
    else
      printf("%c", vet[begin]);
  if (pos>end)
    printf("()");
  printf("\n");
}
char tape::get()
{
  return vet[pos];
}
void tape::set(result r)
{
  vet[pos]=r.insert;
  pos+=r.dir;
}
tape::tape(int n)
{
  vet.resize(n);
  for(int i=0; i<n; ++i)
    vet[i]=' ';
  pos=0;
}
tape::tape()
{
  
}
