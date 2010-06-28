#include "machine.h"

bool tape::read(FILE* fin)
{
    bool startedReading = false;

    for(int i=pos; ; ++i)
    {
        int c = fgetc(fin);
        if (c < 0)
        {
            vet[i]='#';
            return (i != 0);
        }

        if (c == '#')
        {
            return true;
        }
        else if (c == '\r' || c == '\n')
        {
            if (startedReading)
            {
                return true;
            }
            else
            {
                --i;
            }
        }
        else
        {
            startedReading = true;
            vet[i] = c;
            ++my_usedSize;
        }
    }
}

char tape::get()
{
  return vet[pos];
}

void tape::set(result r)
{
  vet[pos] = r.insert;

  if (r.insert == '#' && pos == (my_usedSize - 1))
  {
      while (vet[my_usedSize - 1] == '#')
      {
          --my_usedSize;
      }
  }
  else if (r.insert != '#' && pos > (my_usedSize - 1))
  {
      my_usedSize = pos + 1;
  }

  pos += r.dir;
}

tape::tape(int n)
{
  vet.resize(n);
  for(int i=0; i<n; ++i)
    vet[i]='#';
  pos=0;
  my_usedSize = 0;
}

int tape::usedSize() const
{
    return my_usedSize;
}
