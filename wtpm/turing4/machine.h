#include <string>
#include <map>
#include <vector>
using namespace std;

class state;
class result;
class tape;
class machine;

class state {
 public:
  string name;
  string subroutine;
  bool final;
  map<char, result> action;
  state();
  state(string);
};

class result {
 public:
  int dir;
  state* next;
  char insert;
  result(char d, state* n, int i);
};

class tape {
 public:
  vector<char> vet;
  int pos;
  void read(FILE*);
  void print();
  char get();
  void set(result);
  tape(int);
  tape();
};

class machine {
 public:
  vector<state> vet;
  tape* t;
  state* q;
  bool run();
  bool apply();
  bool read(FILE*);
  void debug();
#ifdef GRAPH_GEN
  bool hasOuterEdges(const state*);
  void generateGraph(FILE*f, const char *ratio, bool lr, bool dl, bool ef);
#endif
  machine(tape*);
};

