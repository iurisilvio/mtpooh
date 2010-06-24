#include <cstdio>
#include <vector>
#include <list>
#include <cstdlib>
#include <cstring>
#include <string>
#include <map>
#include <set>
#include <deque>
#include <stack>
#include <queue>
#include <string>
#include <exception>
using namespace std;

#include "machine.h"
const int tape_size = 30000;

FILE* nth_file(int n, int argc, char* argv[])
{
  FILE* res;
  exception ex;
  if (argc>n)
    res=fopen(argv[n], "r");
  else
    res=stdin;
  if (!res)
    throw ex;
  return res;
}
int main(int argc, char* argv[])
{
  FILE *f1 = nth_file(1, argc, argv), *f2 = nth_file(2, argc, argv);
  tape t(tape_size);
  machine m(&t);
  if (!m.read(f1))
    return 1;
  t.read(f2);
  //m.debug();
  t.print();
  printf("%s\n", m.vet[0].name.c_str());
  printf("During computation:\n");
  if (m.run())
    printf("Computation finished.\n");
  else
    printf("Step Limit Exceeded.\n");
  return 0;
}
