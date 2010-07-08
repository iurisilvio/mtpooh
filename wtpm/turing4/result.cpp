#include "machine.h"

result::result(char d, state* n, int i)
{
  exception se;
  if (d=='L'||d=='l')
    dir=-1;
  else if (d=='R'||d=='r')
    dir=1;
  else if (d=='S'||d=='s')
    dir=0;
  else
    throw se;
  next=n;
  insert=i;
}
