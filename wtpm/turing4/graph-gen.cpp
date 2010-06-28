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

int main(int argc, char* argv[])
{

  if (argc < 2)
  {
      printf("Usage: tpmgg <input file name> [-r RATIO] [-f format] [-o output file name]\n             [-lr] [-dl] [-ef]\n\n\t-lr: graph has left-to-right orientation, instead of top-to-bottom\n\t-dl: an arrow connects each edge label to its associated edge\n\t-f: Format can be one of gif, png, svg, pdf\n\t-ef: Functions (subroutines) are rendered in seperate graphs, only entry\n\t      and exit points added to main graph");
      exit(1);
  }

  const char *ratio = NULL;
  const char *outfile = NULL;
  const char *format = NULL;

  bool lr = false,
       dl = false,
       ef = false;

  for (int i = 2; i < argc; ++i)
  {
      if (strcmp(argv[i], "-lr") == 0) lr = true;
      else if (strcmp(argv[i], "-dl") == 0) dl = true;
      else if (strcmp(argv[i], "-ef") == 0) ef = true;
      else if (strcmp(argv[i], "-r") == 0 && (i + 1 < argc)) ratio = argv[i+1];
      else if (strcmp(argv[i], "-o") == 0 && (i + 1 < argc)) outfile = argv[i+1];
      else if (strcmp(argv[i], "-f") == 0 && (i + 1 < argc)) format = argv[i+1];
  }

  machine m;
  FILE *machine=fopen(argv[1], "r");
  if (!machine)
  {
      printf("Error opening input file");
      exit(2);
  }

  if (!m.read(machine))
  {
      exit(4);
  }

  fclose(machine);

  char graphFileName[1024];
  strcpy(graphFileName, argv[1]);
  strcat(graphFileName, ".graph");

  FILE *graph=fopen(graphFileName, "w+");
  if (!graph)
  {
      printf("Error opening output file");
      exit(3);
  }

  m.generateGraph(graph, ratio, lr, dl, ef);
  fclose(graph);

  char dotCommandLine[1024];

  if (outfile)
    sprintf(dotCommandLine, "dot -T%s %s -o %s", format ? format : "gif", graphFileName, outfile);
  else
    sprintf(dotCommandLine, "dot -T%s %s -O", format ? format : "gif", graphFileName);

  printf("Executing %s", dotCommandLine);
  system(dotCommandLine);
}
