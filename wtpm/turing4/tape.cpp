#include "machine.h"

void tape::read(FILE* fin)
{
  for(int i=pos; ; ++i)
    {
      if (fscanf(fin, " %c", &vet[i])!=1)
	{
	  vet[i]='#';
	  break;
	}
      if (vet[i]=='#')
	{
	  break;
	}
    }
}
void tape::print()
{
  int end, size;
  size=vet.size();
  for(end=size-1; end>pos; --end)
    if (vet[end]!='#')
      break;
  for(int i=0; i<=end; ++i)
    if (i==pos)
      printf("(%c)", vet[i]);
    else
      printf("%c", vet[i]);
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
    vet[i]='#';
  pos=0;
}
tape::tape()
{
  
}
