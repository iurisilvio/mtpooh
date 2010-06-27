#include <cstdio>
#include <ctime>
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

  clock_t start = clock();
  runresults results = m.run();

  // Print run information
  if (results.finished)
  {
    if (results.runsteps.back().s->final)
    {
        printf("Input accepted!\n\n");
    }
    else
    {
        printf("Input rejected!\n\n");
    }
  }
  else
  {
    if (results.loop_end != 0)
    {
        printf("Machine entered in loop (steps %d and %d have identical configurations)\n\n", results.loop_start, results.loop_end);
    }
    else
    {
        printf("Computation aborted. Step limit exceeded!\n\n");
    }
  }

  printf("Final Configuration:\n");
  results.runsteps.back().print(results.runsteps.size() - 1);

  printf("\nStep Count: %d\nRun time: %.3f seconds\n\n", results.runsteps.size(), ((double)clock() - start) / CLOCKS_PER_SEC);

  // Print step-by-step afterwards

  printf("Computation steps:\n");
  for (unsigned int i = 0; i < results.runsteps.size(); ++i)
  {
      results.runsteps[i].print(i);
  }

  return 0;
}
