#include <string>
#include <map>
#include <vector>
#include <cstdio>
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
  bool read(FILE*);
  int usedSize() const;
  char get();
  void set(result);
  tape(int);
  tape();
private:
  int my_usedSize;
};

class instantconfiguration
{
  public:
    string a1;
    state* s;
    string a2;

    void print(int stepIndex) const;
    bool operator < (const instantconfiguration& other) const;
};

class runresults
{
  public:
    vector<instantconfiguration> runsteps;
    bool finished;
    int loop_start;
    int loop_end;
};

class machine {
 public:
  vector<state> vet;
  state* q;
  runresults run(tape *t);
  bool apply();
  bool read(FILE*);
  void debug();
  bool hasOuterEdges(const state*);
  void generateGraph(FILE*f, const char *ratio, bool lr, bool dl, bool ef);
  machine();
private:
  instantconfiguration getInstantConfiguration();
  tape *t;
};
