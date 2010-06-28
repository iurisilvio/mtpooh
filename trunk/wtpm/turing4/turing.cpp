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
const int tape_size = 1000;

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

void runDetailed(machine& m, tape *t)
{
    clock_t start = clock();
    runresults results = m.run(t);

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
}

int main(int argc, char* argv[])
{
  FILE *f1 = nth_file(1, argc, argv),
       *f2 = nth_file(2, argc, argv);
  vector<tape*> tapes;
  machine m;

  if (!m.read(f1))
    return 1;

  do
  {
      tape* t = new tape(tape_size);
      if (t->read(f2))
      {
        tapes.push_back(t);
      }
  } while (f2 != stdin && !feof(f2));

  if (tapes.size() == 1)
  {
      runDetailed(m, tapes[0]);
  }
  else
  {
      int maxTapeSize = 7;

      for (unsigned i = 0; i < tapes.size(); ++i)
      {
          if (tapes[i]->usedSize() > maxTapeSize)
          {
              maxTapeSize = tapes[i]->usedSize();
          }
      }

      printf("Input%*s Result     Steps  State: Tape\n", maxTapeSize - 5, "");
      printf("\n");

      for (unsigned i = 0; i < tapes.size(); ++i)
      {
          runresults results = m.run(tapes[i]);
          const char *status = results.finished ? (results.runsteps.back().s->final ? "accepted" : "rejected") :
                               (results.loop_end == 0) ? "step limit" : "loop";

          const char *input = (results.runsteps[0].a2.size() == 0) ? "<empty>" : results.runsteps[0].a2.c_str();
          printf("%-*s ", maxTapeSize, input);
          printf("%-10s %6d ", status, results.runsteps.size());
          results.runsteps.back().printResumed();
      }
  }

  return 0;
}
